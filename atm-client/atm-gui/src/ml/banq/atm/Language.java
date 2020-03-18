package ml.banq.atm;

import java.util.Locale;
import java.util.ResourceBundle;

public class Language {
    private static Language instance = new Language();

    private ResourceBundle resourceBundle;
    private String language;

    private Language() {}

    public static Language getInstance() {
        return instance;
    }

    public String getLanguage() {
        return language;
    }

    public void changeLanguage(String language) {
        this.language = language;
        resourceBundle = ResourceBundle.getBundle("resources.languages.strings", new Locale(language));
    }

    public static String getString(String key) {
        return instance.resourceBundle.getString(key);
    }
}
