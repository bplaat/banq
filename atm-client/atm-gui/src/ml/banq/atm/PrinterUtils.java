package ml.banq.atm;

// The static printer utils class
public class PrinterUtils {
    // Pad a left and right string with the right amount of spaces
    public static String pad(String left, String right) {
        int spaces = Config.PRINTER_PAPER_WIDTH - left.length() - right.length();
        String line = left;
        for (int i = 0; i < spaces; i++) {
            line += " ";
        }
        return line + right;
    }

    // Center a string with spaces
    public static String center(String text) {
        int spaces = (Config.PRINTER_PAPER_WIDTH - text.length()) / 2;
        String line = "";
        for (int i = 0; i < spaces; i++) {
            line += " ";
        }
        return line + text;
    }

    // Create a horizontal line string
    public static String horizontalLine() {
        String line = "";
        for (int i = 0; i < Config.PRINTER_PAPER_WIDTH; i++) {
            line += "-";
        }
        return line;
    }
}
