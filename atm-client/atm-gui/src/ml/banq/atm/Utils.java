package ml.banq.atm;

public class Utils {
    public static final int PRINTER_WIDTH = 32;

    public static String printerPad(String left, String right) {
        int spaces = PRINTER_WIDTH - left.length() - right.length();
        String line = left;
        for (int i = 0; i < spaces; i++) {
            line += " ";
        }
        return line + right;
    }

    public static String printerCenter(String text) {
        int spaces = (PRINTER_WIDTH - text.length()) / 2;
        String line = "";
        for (int i = 0; i < spaces; i++) {
            line += " ";
        }
        return line + text;
    }

    public static String printerHorizontalLine() {
        String line = "";
        for (int i = 0; i < PRINTER_WIDTH; i++) {
            line += "-";
        }
        return line;
    }
}
