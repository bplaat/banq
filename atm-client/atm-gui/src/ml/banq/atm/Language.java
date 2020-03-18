package ml.banq.atm;

import java.io.InputStreamReader;
import java.util.Properties;

public class Language {
    private static Language instance = new Language();

    private String language;
    private Properties properties;

    private Language() {}

    public static Language getInstance() {
        return instance;
    }

    public String getLanguage() {
        return language;
    }

    public void changeLanguage(String language) {
        this.language = language;

        try {
            properties = new Properties();
            properties.load(new InputStreamReader(getClass().getResource("/resources/languages/strings_" + language + ".properties").openStream(), "UTF8"));
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public static String getString(String key) {
        try {
            return instance.properties.getProperty(key);
        } catch (Exception e) {
            return null;
        }
    }
}
