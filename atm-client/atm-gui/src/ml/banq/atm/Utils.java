package ml.banq.atm;

import java.awt.image.BufferedImage;
import java.awt.Image;
import javax.imageio.ImageIO;
import javax.swing.ImageIcon;

public class Utils {
    public static ImageIcon loadImage(String filename, int width, int height) {
        try {
            BufferedImage image = ImageIO.read(Utils.class.getResource("/resources/images/" + filename));
            return new ImageIcon(image.getScaledInstance(width, height, Image.SCALE_SMOOTH));
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }

    public static String printerPad(String left, String right) {
        int spaces = Config.PRINTER_PAPER_WIDTH - left.length() - right.length();
        String line = left;
        for (int i = 0; i < spaces; i++) {
            line += " ";
        }
        return line + right;
    }

    public static String printerCenter(String text) {
        int spaces = (Config.PRINTER_PAPER_WIDTH - text.length()) / 2;
        String line = "";
        for (int i = 0; i < spaces; i++) {
            line += " ";
        }
        return line + text;
    }

    public static String printerHorizontalLine() {
        String line = "";
        for (int i = 0; i < Config.PRINTER_PAPER_WIDTH; i++) {
            line += "-";
        }
        return line;
    }
}
