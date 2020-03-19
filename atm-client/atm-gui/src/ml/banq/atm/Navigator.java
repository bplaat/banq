package ml.banq.atm;

import javax.swing.JLabel;
import javax.swing.JPanel;

// The navigator singleton
public class Navigator extends JPanel {
    private static final long serialVersionUID = 1;

    // The navigator singleton instance
    private static Navigator instance = new Navigator();

    private Page page;

    private Navigator() {
        // Set the navigator to use absolute layout (for the logo)
        setLayout(null);

        // Create the top left logo image
        JLabel logoImage = new JLabel(ImageUtils.loadImage("logo.png", 96, 96));
        logoImage.setBounds(Paddings.NORMAL, Paddings.NORMAL, 96, 96);
        add(logoImage);

        // Create the top left logo label
        JLabel logoLabel = new JLabel(Config.BANK_NAME);
        logoLabel.setFont(Fonts.LOGO);
        logoLabel.setBounds(Paddings.NORMAL * 2 + 96, Paddings.NORMAL + 4, 256, 96);
        add(logoLabel);
    }

    // Get a navigator instance
    public static Navigator getInstance() {
        return instance;
    }

    // Get the current page
    public Page getPage() {
        return page;
    }

    // Change the navigator page with a beeper
    public void changePage(Page new_page) {
        changePage(new_page, true);
    }

    // Change the navigator page
    public void changePage(Page new_page, boolean beeper) {
        // Change the page
        if (page != null) {
            remove(page);
        }
        page = new_page;
        page.setBounds(0, 0, App.getInstance().getWindowWidth(), App.getInstance().getWindowHeight());
        add(page);

        // Repaint the window
        App.getInstance().repaintWindow();

        // Make a default beep
        if (beeper) {
            App.getInstance().sendBeeper(440, 250);
        }
    }

    // Resize the current page
    public void resizePage(int width, int height) {
        page.setBounds(0, 0, width, height);
    }
}
