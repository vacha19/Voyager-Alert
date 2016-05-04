package facebookapi;

import com.restfb.Connection;
import com.restfb.DefaultFacebookClient;
import com.restfb.FacebookClient;
import com.restfb.types.Post;
import java.io.IOException;
import java.util.List;

/**
 *
 * @author dsfounis
 */
public class FacebookAPI {

    public static void main(String[] args) throws IOException {

        String MY_ACCESS_TOKEN = "CAAP6qJvz3SUBAMQfMFZB2IpZA5B3fdadSruSgNnCLrNiX6qYAJMrVitp0eZBzYEZAoPjxyO6P4SiZAW5I8S4TYGhkarCovZBbbjBGQy69mSZCAwGbFputkhod42dxkZByPAsJV9iWYkGQbOZC6lGBGIV0db1Y2fpEZCga3tyItiWRHF7tZBAy9w0WCRIkt3jpnl4rjD6bwrkjJ6pQZDZD";
        FacebookClient facebookClient = new DefaultFacebookClient(MY_ACCESS_TOKEN);

        String[] websites = new String[]{"bbcnews", "cnninternational", "abcnews", "hindustantimes", "usatoday", "theguardian", "foxnews", "nbcnews", "cbsnews", "msn", "reuters", "apnews", "cnbc", "aljazeera"};

        String[] terms = new String[]{"terrorist", "gunman", "shooting", "gunshots", "gun shots", "explosion",
            "mass shooting", "hiding", "heard gun", "killed", "dead", "killed"};

        
        
        for (String website : websites) {
            int count = 1;
            System.out.println(website);
            Connection<Post> pageFeed = facebookClient.fetchConnection(website+"/feed", Post.class);
            for (List<Post> feed : pageFeed) {
                if(count>100)
                    break;
                for (Post post : feed) {
                    for (String s : terms) {
                        if (post.toString().contains(s)) {
                            System.out.print((post.getCreatedTime().getTime()) / 1000 + "|2|" + post.getMessage());
                            System.out.println();
                        }
                    }
                    count++;
                }
            }
        }
    }
}
