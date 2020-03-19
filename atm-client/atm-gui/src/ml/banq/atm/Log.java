package ml.banq.atm;

public class Log {
    public static void debug(String text) {
        if (Config.DEBUG) {
            System.out.print("[DEBUG] " + text);

            if (text.charAt(text.length() - 1) != '\n') {
                System.out.print('\n');
            }
        }
    }

    public static void debug(Throwable throwable) {
        System.out.print("[DEBUG] ");
        throwable.printStackTrace();
    }

    public static void info(String text) {
        System.out.print("[INFO] " + text);

        if (text.charAt(text.length() - 1) != '\n') {
            System.out.print('\n');
        }
    }

    public static void info(Throwable throwable) {
        System.out.print("[INFO] ");
        throwable.printStackTrace();
    }

    public static void warning(String text) {
        System.out.print("[WARNING] " + text);

        if (text.charAt(text.length() - 1) != '\n') {
            System.out.print('\n');
        }
    }

    public static void warning(Throwable throwable) {
        System.out.print("[WARNING] ");
        throwable.printStackTrace();
    }

    public static void error(String text) {
        System.out.print("[ERROR] " + text);

        if (text.charAt(text.length() - 1) != '\n') {
            System.out.print('\n');
        }
    }

    public static void error(Throwable throwable) {
        System.out.print("[ERROR] ");
        throwable.printStackTrace();
    }
}
