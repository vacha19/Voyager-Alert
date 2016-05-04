#ifdef __cplusplus
    #include <cstdlib>
#else
    #include <stdlib.h>
#endif

#include <iostream>
#include <fstream>
#include <time.h>

#include <sstream>

#include <openssl/bio.h>
#include <openssl/evp.h>
#include <openssl/buffer.h>
#include <stdint.h>

#include <curl/curl.h>


#include <stdint.h>
#include <stdlib.h>



#include <string>
#include <list>
#include <map>
using namespace std;

#include "mysql_connection.h"

#include <cppconn/driver.h>
#include <cppconn/exception.h>
#include <cppconn/resultset.h>
#include <cppconn/statement.h>
#include <cppconn/prepared_statement.h>

#include <SDL/SDL.h>
#include "SDL_ttf.h"

char *base64_encode(const unsigned char *data, size_t input_length, size_t *output_length);

#if SDL_BYTEORDER == SDL_BIG_ENDIAN
    Uint32 rmask = 0xff000000;
    Uint32 gmask = 0x00ff0000;
    Uint32 bmask = 0x0000ff00;
    Uint32 amask = 0x000000ff;
#else
    Uint32 rmask = 0x000000ff;
    Uint32 gmask = 0x0000ff00;
    Uint32 bmask = 0x00ff0000;
    Uint32 amask = 0xff000000;
#endif

const double TIME_DIFF = 8.0;
const double TIME_RECENT = (60*60*6);
const double TIME_PAST = TIME_RECENT*TIME_DIFF;
//const double TIME_RATIO = TIME_PAST/double(TIME_RECENT);


struct pin_data {
    unsigned long pid;
    unsigned long sid;
    time_t time;
    float score;
    time_t event;
};

struct place_data {
    unsigned long pid;
    char* name;
    float score;
    float baseline;
    double geo_lat;
    double geo_long;
    bool active;
    time_t event;
    Sint16 lastpos;
    bool posted;
};

typedef list<unsigned long> active_t;
active_t active;

typedef map<unsigned long, place_data> places_t;
places_t places;


struct event_data {
    unsigned long pid;
    time_t event;
    struct {
        unsigned long sid;
        float score;
        char text[140];
    } top[5];
};

typedef map<time_t, event_data> event_t;
event_t events;


typedef list<pin_data> pins_t;
pins_t pins;


CURL *curl;
CURLcode res;

void postEvent(time_t event) {

    std::ostringstream Data;
    event_data &ev = events[event];
    place_data &place = places[ev.pid];
    static char buffer[4096];
    Data << "new="<< event;
    Data << "&lat="<< place.geo_lat;
    Data << "&long="<< place.geo_long;

    Data << "&start="<< place.event;


    size_t retlen;
    const char* name = base64_encode((const unsigned char*)place.name, strlen(place.name), &retlen);

    Data << "&text="<< name;


    std::string s = Data.str();
    strncpy(buffer, s.c_str(),4096);

//    cout << "Posted: " << buffer << endl;


    curl_easy_setopt(curl, CURLOPT_POSTFIELDS, buffer);


    res = curl_easy_perform(curl);
    if(res != CURLE_OK)
      cout << "curl_easy_perform() failed:" << curl_easy_strerror(res) << endl;


}


void absorb(pins_t::iterator& it) {
    pin_data &pin = (*it);
    pins_t::iterator pos = it;
    pos--;
    while( pos != pins.begin() && (*pos).sid == pin.sid  ) {
        (*pos).event = pin.event;
        places[(*pos).pid].score -= (*pos).score;
        places[(*pos).pid].baseline -= (*pos).score;
        pos--;
    }
    it++;


    while( it != pins.end() && (*it).sid == pin.sid  ) {
        (*it).event = pin.event;
        it++;
    }
}

void resetScores() {
    for(auto&& p : places) {
        p.second.score=0;
        p.second.baseline=0;
        p.second.event=0;
        p.second.active=false;
        p.second.lastpos=0;
    }
    for(auto &p : pins) {
        p.event=0;
    }
    active.clear();
}


int main ( int argc, char** argv )
{
    ofstream logger("log.txt", std::ofstream::out);

    sql::Driver * driver = get_driver_instance();

    sql::Connection* con = driver->connect("127.0.0.1", "cs125", "cs125");
    con->setSchema("cs125");

    sql::PreparedStatement* pstmt;
    sql::ResultSet* res;
//        pstmt->setInt(1, now-60);
  //      pstmt->setInt(2, now);

  //'323786'
  // 2288115


    //delete pstmt;

    pstmt = con->prepareStatement("SELECT * FROM cs125.places");
    res = pstmt->executeQuery();
    while (res->next()) {
        string admin0 = res->getString("admin0");
        string admin1 = res->getString("admin1");
        string admin2 = res->getString("admin2");
        place_data p;
        p.pid = res->getInt("pid");
        string name = res->getString("name");
        if( admin2 != "" ) name += ", "+admin2;
        if( admin1 != "" ) name += ", "+admin1;
        if( admin0 != "" ) name += ", "+admin0;

        p.name = new char[name.size() + 1];
        std::copy(name.begin(), name.end(), p.name);
        p.name[name.size()] = '\0';

        p.score = 0;
        p.baseline = 0;
        p.geo_lat = res->getDouble("lat");
        p.geo_long = res->getDouble("long");
        p.active = false;
        places[p.pid]=p;
    }
    delete res;
    delete pstmt;

    pstmt = con->prepareStatement("SELECT pid,sid,time,score FROM cs125.pins WHERE `time` > ? && `time`<(SELECT MAX(`time`) FROM cs125.sources WHERE `score` IS NOT NULL) ORDER BY `time` ASC, `sid` ASC LIMIT 200000");
    sql::PreparedStatement* gettext = con->prepareStatement("SELECT text FROM cs125.sources WHERE `sid` = ?");


    auto itbase = pins.begin();
    auto itscore = pins.begin();
    auto itnow = pins.begin();


    curl = curl_easy_init();
    curl_easy_setopt(curl, CURLOPT_URL, "http://uci.alware.org/activity.php");
    /* example.com is redirected, so we tell libcurl to follow redirection */
    curl_easy_setopt(curl, CURLOPT_FOLLOWLOCATION, 1L);

    //cout << date << " Score: " << res->getDouble("score") << " Pid: " << << endl;




    // initialize SDL video
    if ( SDL_Init( SDL_INIT_VIDEO ) < 0 )
    {
        printf( "Unable to init SDL: %s\n", SDL_GetError() );
        return 1;
    }

    // make sure SDL cleans up before exit
    atexit(SDL_Quit);

    // create a new window
    SDL_Surface* screen = SDL_SetVideoMode(1920, 1080, 32, SDL_HWSURFACE|SDL_DOUBLEBUF|SDL_FULLSCREEN);
    if ( !screen )
    {
        printf("Unable to set 640x480 video: %s\n", SDL_GetError());
        return 1;
    }

    if( TTF_Init() == -1 )
    {
        return false;
    }

    TTF_Font* font = TTF_OpenFont( "FreeSans.ttf", 28 );
    TTF_Font* smallfont = TTF_OpenFont( "FreeSans.ttf", 14 );

    // load an image
    SDL_Surface* earth = SDL_LoadBMP("earth.bmp");
    SDL_Surface* blip = SDL_LoadBMP("blip.bmp");
    blip->format->Amask = 0xFF000000;
    blip->format->Ashift = 24;
    if (!earth)
    {
        printf("Unable to load bitmap: %s\n", SDL_GetError());
        return 1;
    }

    const Uint32 WIDTH = 1000;
    const Uint32 HEIGHT = 1000;
    const Uint32 NAMES = 300;
    const Uint32 MAPW = 620;
    const Uint32 MAPH = 310;

    const double THREASH = 50.0;


    SDL_Rect buffrect;
    SDL_Rect windowrect;
    buffrect.x = 0;
    buffrect.y = 0;
    buffrect.w = WIDTH;
    buffrect.h = HEIGHT;

    windowrect.x = 0;
    windowrect.h = HEIGHT;
    windowrect.y = 0;

    SDL_Rect earthrect;
    earthrect.x = WIDTH+NAMES;
    earthrect.y =0;
    earthrect.w=MAPW;
    earthrect.h=MAPH;

    SDL_Surface* buffer = SDL_CreateRGBSurface(SDL_HWSURFACE, WIDTH, HEIGHT, 32, rmask, gmask, bmask, amask);

    SDL_Surface* earthoverlay = SDL_CreateRGBSurface(SDL_HWSURFACE, MAPW, MAPH, 32, rmask, gmask, bmask, amask);

    SDL_FillRect(buffer, 0, SDL_MapRGB(buffer->format, 255, 255, 255));

    SDL_Rect textrect;


    buffrect.x = WIDTH/2-10;
    buffrect.w = 20;

    SDL_FillRect(buffer, &buffrect, SDL_MapRGB(buffer->format, 255, 0, 0));

    time_t now = 1457853933; //pins.front().time - TIME_PAST; //1458026531-604800);1457886900-(60*60*2); //
    unsigned long tick = (1457853933/60)*60;



    char date[32];
    strftime(date, sizeof(date), "%Y-%m-%d %r", gmtime(&now));

    cout << "Start is: " << date << endl;

    // program main loop
    bool done = false;
    bool first_run = true;

    SDL_Color fontcolor={0,0,0};

    SDL_Rect namesrect;
    namesrect.x = WIDTH;
    namesrect.y = 0;
    namesrect.w = NAMES;
    namesrect.h = HEIGHT;

    SDL_Rect namesloc;
    namesloc.x = WIDTH;
    namesloc.y = 0;
    namesloc.w = 0;
    namesloc.h = 0;

    SDL_Rect textarea;
    textarea.x = WIDTH+NAMES;
    textarea.y = MAPH;
    textarea.w = MAPW;
    textarea.h = HEIGHT-MAPH;

    //Uint32 namesback = SDL_MapRGB(screen->format, 45, 115, 255);

    Uint32 graphcolor[2];
    graphcolor[0] = SDL_MapRGB(buffer->format, 20, 20, 20);
    graphcolor[1] = SDL_MapRGB(buffer->format, 50, 50, 50);

    bool paused = true;
    bool damaged = true;
    bool cleargraph = false;
    bool more = true;
    bool fastforward  = false;
    bool fullscreen = true;
    time_t recent = 0;
    while (!done)
    {

        SDL_Event event;
        while (SDL_PollEvent(&event))
        {
            // check for messages
            switch (event.type)
            {
                // exit if the window is closed
            case SDL_QUIT:
                done = true;
                break;
            case SDL_KEYDOWN:
                {
                    if( event.key.keysym.sym ==  SDLK_ESCAPE) {
                        done = true;
                    }else if( event.key.keysym.sym == SDLK_0 ) {
                        resetScores();
                        now = 1457875800-3600;  // Grad-Bassam
                        itbase = pins.begin();
                        itscore = pins.begin();
                        itnow = pins.begin();
                        cleargraph = true;
                    }else if( event.key.keysym.sym == SDLK_1 ) {
                        resetScores();
                        now = 1457886900-3600;  // Ankara
                        itbase = pins.begin();
                        itscore = pins.begin();
                        itnow = pins.begin();//now=1457890140
                        cleargraph = true;
                    }else if( event.key.keysym.sym == SDLK_2 ) {
                        cleargraph = true;
                    }else if( event.key.keysym.sym == SDLK_f ) {
                        fullscreen = !fullscreen;

                    }else if( event.key.keysym.sym == SDLK_SPACE ) {
                        paused = !paused;
                        fastforward = false;

                    }else if( event.key.keysym.sym == SDLK_RIGHT ) {
                        fastforward = !fastforward;
                    }
                    break;
                }
            } // end switch
        } // end of message processing


        if( more == true || time(NULL)%10==0) {
            pstmt->setInt(1, recent);
            res = pstmt->executeQuery();
            more = false;
            while (res->next()) {
                //cout << ".";
                pin_data p;
                p.pid = res->getInt("pid");
                p.sid = res->getInt("sid");
                recent = p.time = res->getInt("time");
                p.score = res->getDouble("score");
                p.event = 0;
                pins.push_back(p);
                if( itnow == pins.end() ) itnow--;
                if( itscore == pins.end() ) itscore--;
                if( itbase == pins.end() ) itbase--;
                more = true;
            }
            delete res;
        }

        while(true) {
            if( itnow == pins.end() ) break;
            pin_data &pin = (*itnow);
            if( pin.time >= now ) break;
           // cout <<" " <<  pin.time << "<" << now << " \n";

            place_data &place = places[pin.pid];
            if( pin.event == 0 ) {
                place.score += pin.score;
                place.baseline += pin.score;
                double diff = place.score- (place.baseline/TIME_DIFF );
                if( !first_run && !place.active ) {

                    if( diff > THREASH ) {
                        //cout << place.name << " is now Active! because of sid=" << pin.sid << " at=" << pin.time << " now=" << now << endl;
                        event_data &ev = events[pin.time];
                        ev.pid = pin.pid;
                        for(auto &s: ev.top) {
                            s.sid=0;
                            s.score=0;
                            s.text[0]=0;
                        }
                        place.active = true;
                        place.event = pin.time;
                        place.lastpos=0;
                        fastforward = false;
                        active.push_back(place.pid);
                    }
                }
                if( place.active ) {
                    event_data &ev = events[place.event];
                    for(auto &s: ev.top) {
                        double ps = pin.score / double(1+pin.time-place.event);
                        if( ps > s.score) {
                            gettext->setInt(1, s.sid);
                            res = gettext->executeQuery();
                            string text;
                            if (res->next()) {
                                text = res->getString("text");

                                //cout << "Loaded: "<< s.text << endl;
                            }
                            delete res;
                            for(auto &s2: ev.top) {
                                if( text.compare(s2.text) == 0 ) break;
                            }
                            s.score = ps;
                            s.sid=pin.sid;
                            std::copy_n(text.begin(), 139, s.text);
                            s.text[text.size()] = '\0';

                            break;
                        }

                    }

                    absorb(itnow);
                    if( !place.posted && diff > 1.3*THREASH ) {
                            place.posted=true;
                        postEvent(place.event);
                    }
                }else{
                    itnow++;
                }
            }
        }



        time_t past = now - TIME_RECENT;
        while(true) {
            if( itscore == pins.end() ) break;
            pin_data &pin = (*itscore);
            if( pin.time >= past ) break;
            place_data &place = places[pin.pid];
            if( pin.event == 0 ) {
                place.score -= pin.score;
                if( place.active ) {
                     double diff = place.score- (place.baseline/TIME_DIFF );
                    if( diff < THREASH ) {
                        place.active = false;
                        fastforward = false;
                        //cout << place.name << " is now Inactive!" << endl;
                        active.remove(place.pid);
                    }
                }
            }
            itscore++;

        }

        past = now - TIME_RECENT;
        while(true) {
            //if( itbase == pins.end() ) done = true;
            if( itbase == pins.end() ) break;

            pin_data &pin = (*itbase);

            if( pin.time >= past ) break;
            if( pin.event == 0 ) {
                places[pin.pid].baseline -= pin.score;
            }
            itbase++;
        }

        first_run = false;

        // message processing loop




        if( !paused ) {
            Uint32 offset = tick % WIDTH;

            SDL_Rect point;
            point.x = offset;

            point.w = 1;
            point.y = 0;

            unsigned int hour = (now/(60*60))%2;
            if( cleargraph ) {
                SDL_FillRect(buffer, 0, graphcolor[hour]);
                cleargraph = false;
            }
            if( !fastforward ) {
                SDL_FillRect(screen, &namesrect, SDL_MapRGB(screen->format, 45, 115, 255));
                SDL_BlitSurface(earth, 0, screen, &earthrect);
                SDL_FillRect(screen, &textarea, SDL_MapRGB(screen->format, 0, 0, 0));


                strftime(date, sizeof(date), "%Y-%m-%d %r", gmtime(&now));
                SDL_Surface* text_surface = TTF_RenderUTF8_Solid(font,date,fontcolor);
                SDL_BlitSurface(text_surface,NULL,screen,&earthrect);
                SDL_FreeSurface(text_surface);
            }
            point.h = HEIGHT;
            SDL_FillRect(buffer, &point, graphcolor[hour]);
            point.h = 3;

            int TC = 0;
            for( auto act: active) {
                place_data &place = places[act];
                Uint32 y = place.score- (place.baseline/TIME_DIFF )-THREASH;
                if( y > HEIGHT) y = HEIGHT;

                namesloc.y = HEIGHT-y;
                if( namesloc.y<30 ) namesloc.y =30;

                if( y >= place.lastpos ) {
                    point.h = y-place.lastpos;
                    point.y = HEIGHT-y;
                }else{
                    point.h = place.lastpos-y;
                    point.y = HEIGHT-place.lastpos;
                }
                place.lastpos = y;
                if( point.h < 4 ) point.h = 3;
                SDL_FillRect(buffer, &point, SDL_MapRGB(buffer->format, 180, 0, 0));

                if( !fastforward ) {
                    SDL_Rect bliprect;
                    bliprect.x = earthrect.x-6+ (MAPW * ((place.geo_long+180.0)/360.0));
                    bliprect.y = earthrect.y-6+ (MAPH * (90.0-place.geo_lat)/180.0);
                    bliprect.w = bliprect.h = 11;
                    SDL_BlitSurface(blip, 0, screen, &bliprect);

                    SDL_Surface* placename = TTF_RenderUTF8_Solid(smallfont,places[act].name,fontcolor);
                    SDL_BlitSurface(placename,NULL,screen,&namesloc);
                    SDL_FreeSurface(placename);

                    if( TC < 4 && place.posted ) {

                        textrect.x=WIDTH+NAMES+10;
                        textrect.y=MAPH+5+(TC*180);
                        SDL_Surface* placename = TTF_RenderUTF8_Solid(smallfont,places[act].name,{255,255,255});
                        SDL_BlitSurface(placename,NULL,screen,&textrect);
                        SDL_FreeSurface(placename);
                        textrect.x=WIDTH+NAMES+10;
                        event_data &ev = events[place.event];
                        for(auto &s : ev.top) {
                            textrect.y+=30;
                            if( s.sid ) {
                                SDL_Surface* placename = TTF_RenderUTF8_Solid(smallfont,s.text,{255,255,255});
                                SDL_BlitSurface(placename,NULL,screen,&textrect);
                                SDL_FreeSurface(placename);
                            }
                        }
                        TC++;
                    }



                }
            }

             if( !fastforward ) {

                Uint32 rem = WIDTH - offset;

                if( offset == 0 ) {
                    buffrect.x = 0;
                    buffrect.w = WIDTH;
                    SDL_BlitSurface(buffer, &buffrect, screen, &buffrect);
                }else{
                    buffrect.x = 0;
                    buffrect.w = offset;
                    windowrect.x = rem;
                    windowrect.w = offset;
                    SDL_BlitSurface(buffer, &buffrect, screen, &windowrect);

                    buffrect.x = offset;
                    buffrect.w = rem;
                    windowrect.x = 0;
                    windowrect.w = rem;
                    SDL_BlitSurface(buffer, &buffrect, screen, &windowrect);
                }




                damaged = true;
            }


            if( now < recent && now < time(NULL)-130) {
                tick++;
                now+=120;
            }else{
                fastforward = false;
            }
        }
        if( damaged ) {
            SDL_Flip(screen);
            damaged = false;
        }
        if( !fastforward ) SDL_Delay(50);
    } // end main loop

    // free loaded bitmap
    SDL_FreeSurface(earth);

    logger.close();

    curl_easy_cleanup(curl);

    curl_global_cleanup();

    // all is well ;)
    printf("Exited cleanly\n");
    return 0;
}


static char encoding_table[] = {'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
                                'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
                                'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
                                'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f',
                                'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
                                'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
                                'w', 'x', 'y', 'z', '0', '1', '2', '3',
                                '4', '5', '6', '7', '8', '9', '+', '/'};
static int mod_table[] = {0, 2, 1};

char *base64_encode(const unsigned char *data,
                    size_t input_length,
                    size_t *output_length) {

    *output_length = 4 * ((input_length + 2) / 3);

    char *encoded_data = (char*) malloc(*output_length);
    if (encoded_data == NULL) return NULL;

    for (int i = 0, j = 0; i < input_length;) {

        uint32_t octet_a = i < input_length ? (unsigned char)data[i++] : 0;
        uint32_t octet_b = i < input_length ? (unsigned char)data[i++] : 0;
        uint32_t octet_c = i < input_length ? (unsigned char)data[i++] : 0;

        uint32_t triple = (octet_a << 0x10) + (octet_b << 0x08) + octet_c;

        encoded_data[j++] = encoding_table[(triple >> 3 * 6) & 0x3F];
        encoded_data[j++] = encoding_table[(triple >> 2 * 6) & 0x3F];
        encoded_data[j++] = encoding_table[(triple >> 1 * 6) & 0x3F];
        encoded_data[j++] = encoding_table[(triple >> 0 * 6) & 0x3F];
    }

    for (int i = 0; i < mod_table[input_length % 3]; i++)
        encoded_data[*output_length - 1 - i] = '=';

    return encoded_data;
}



