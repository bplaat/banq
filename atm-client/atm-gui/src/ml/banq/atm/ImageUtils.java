package ml.banq.atm;

import java.awt.image.BufferedImage;
import java.awt.Image;
import javax.imageio.ImageIO;
import javax.swing.ImageIcon;

// The image utils class
public class ImageUtils {
    // Load a image resource and resize it
    public static ImageIcon loadImage(String filename, int width, int height) {
        try {
            BufferedImage image = ImageIO.read(ImageUtils.class.getResource("/resources/images/" + filename));
            return new ImageIcon(image.getScaledInstance(width, height, Image.SCALE_SMOOTH));
        } catch (Exception exception) {
            Log.error(exception);
            return null;
        }
    }
}
