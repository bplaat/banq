package ml.banq.atm;

// Static log class
public class Log {
    private Log() {}

    // Print debug message when debug mode is on
    public static void debug(String text) {
        if (Config.DEBUG) {
            System.out.print("[DEBUG] " + text);

            if (text.charAt(text.length() - 1) != '\n') {
                System.out.print('\n');
            }
        }
    }

    // Print debug throwable
    public static void debug(Throwable throwable) {
        if (Config.DEBUG) {
            System.out.print("[DEBUG] ");
            throwable.printStackTrace();
        }
    }

    // Print info message
    public static void info(String text) {
        System.out.print("[INFO] " + text);

        if (text.charAt(text.length() - 1) != '\n') {
            System.out.print('\n');
        }
    }

    // Print info throwable
    public static void info(Throwable throwable) {
        System.out.print("[INFO] ");
        throwable.printStackTrace();
    }

    // Print warning message
    public static void warning(String text) {
        System.out.print("[WARNING] " + text);

        if (text.charAt(text.length() - 1) != '\n') {
            System.out.print('\n');
        }
    }

    // Print warning throwable
    public static void warning(Throwable throwable) {
        System.out.print("[WARNING] ");
        throwable.printStackTrace();
    }

    // Print error message
    public static void error(String text) {
        System.out.print("[ERROR] " + text);

        if (text.charAt(text.length() - 1) != '\n') {
            System.out.print('\n');
        }

        System.exit(1);
    }

    // Print error throwable
    public static void error(Throwable throwable) {
        System.out.print("[ERROR] ");
        throwable.printStackTrace();
        System.exit(1);
    }
}
