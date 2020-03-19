package ml.banq.atm;

import java.awt.Font;
import javax.swing.JLabel;

// The static fonts class
public class Fonts {
    public static final Font DEFAULT = new JLabel().getFont();
    public static final Font LOGO = new Font(DEFAULT.getName(), Font.BOLD, 40);
    public static final Font HEADER = new Font(DEFAULT.getName(), Font.BOLD, App.getInstance().getWindowWidth() / 200 * 7);
    public static final Font NORMAL = new Font(DEFAULT.getName(), Font.PLAIN, App.getInstance().getWindowWidth() / 200 * 4);
    public static final Font NORMAL_BOLD = new Font(DEFAULT.getName(), Font.BOLD, NORMAL.getSize());
}
