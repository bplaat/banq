package ml.banq.atm;

import java.io.InputStreamReader;
import java.util.Properties;

// The language singleton class
public class Language {
    // The language singleton instance
    private static Language instance = new Language();

    private String language;
    private Properties properties;

    private Language() {}

    // Get a language instance
    public static Language getInstance() {
        return instance;
    }

    // Get the current language
    public String getLanguage() {
        return language;
    }

    // Change the current language and read the properties file
    public void changeLanguage(String language) {
        this.language = language;

        try {
            properties = new Properties();
            properties.load(new InputStreamReader(getClass().getResource("/resources/languages/strings_" + language + ".properties").openStream(), "UTF8"));
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    // Get a language string from the current language
    public static String getString(String key) {
        try {
            return instance.properties.getProperty(key);
        } catch (Exception e) {
            return null;
        }
    }
}
