package ml.banq.atm;

// The static config class
public class Config {
    private Config() {}

    public static final boolean DEBUG = false;

    public static final boolean FULLSCREEN_MODE = true;
    public static final String BANK_NAME = "Banq";
    public static final String BANQ_API_URL = "https://banq.ml/api";
    public static final String BANQ_API_DEVICE_KEY = "d5b789b71530947b7e6bc0f23afafbba";
    public static final String DEFAULT_LOCATION = "Unkown?";

    public static final String[][] LANGUAGES = {
        { "nl", "Nederlands" },
        { "en", "English" },
        { "de", "Deutsche" },
        { "fr", "Français" },
        { "es", "Español" },
        { "ru", "Русский" }
    };

    public static final String[] ADMIN_RFID_UIDS = { "4a360c0b" };
    public static final int[] DEFAULT_AMOUNTS = { 5, 10, 20, 50, 70, 100, 200 };
    public static final int[] ISSUE_AMOUNTS = { 5, 10, 20, 50 };
    public static final int PRINTER_PAPER_WIDTH = 32;
}
