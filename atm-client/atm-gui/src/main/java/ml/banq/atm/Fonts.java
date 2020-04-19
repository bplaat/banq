package ml.banq.atm;

import java.awt.Font;
import javax.swing.JLabel;

// The static fonts class
public class Fonts {
    private Fonts() {}

    public static final Font DEFAULT = new JLabel().getFont();
    public static final Font LOGO = DEFAULT.deriveFont(Font.BOLD, 40.0f);
    public static final Font HEADER = DEFAULT.deriveFont(Font.BOLD, Math.round(App.getInstance().getWindowWidth() / 200 * 7));
    public static final Font NORMAL = DEFAULT.deriveFont((float)Math.round(App.getInstance().getWindowWidth() / 200 * 4));
    public static final Font NORMAL_BOLD = NORMAL.deriveFont(Font.BOLD);
    public static final Font SMALL = DEFAULT.deriveFont((float)Math.round(App.getInstance().getWindowWidth() / 200 * 3));
}
