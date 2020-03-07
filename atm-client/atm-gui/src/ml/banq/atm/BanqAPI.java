package ml.banq.atm;

public class BanqAPI {
    private static String key;
    private static String rfid;
    private static String pincode;

    public static void setKey(String key) {
        BanqAPI.key = key;
    }

    public static String getKey() {
        return key;
    }

    public static void setRFID(String rfid) {
        BanqAPI.rfid = rfid;
    }

    public static String getRFID() {
        return rfid;
    }

    public static void setPincode(String pincode) {
        BanqAPI.pincode = pincode;
    }

    public static String getPincode() {
        return pincode;
    }

    public static boolean withdraw(float amount) {
        return true;
    }
}
