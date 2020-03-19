package ml.banq.atm;

// The static config class
public class Config {
    public static final String BANK_NAME = "Banq";
    public static final String BANQ_API_URL = "http://banq.local/api";
    public static final String DEVICE_KEY = "38cd34142710c0b70419cb36dc2de1ae";
    public static final String DEVICE_LOCATION = "Rotterdam";
    public static final String[] ADMIN_RFID_UIDS = { "4a360c0b" };
    public static final String[] LANGUAGES = { "nl", "en", "de", "fr" };
    public static final int[] DEFAULT_AMOUNTS = { 5, 10, 20, 50, 70, 100, 200 };
    public static final int PRINTER_PAPER_WIDTH = 32;
    public static final boolean FULLSCREEN_MODE = true;
}
