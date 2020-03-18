package ml.banq.atm;

import javax.swing.JLabel;
import javax.swing.JPanel;

public class Navigator extends JPanel {
    private static final long serialVersionUID = 1;

    private static Navigator instance = new Navigator();

    Page page;

    private Navigator() {
        setLayout(null);

        JLabel logoImage = new JLabel(Utils.loadImage("logo.png", 96, 96));
        logoImage.setBounds(Paddings.NORMAL, Paddings.NORMAL, 96, 96);
        add(logoImage);

        JLabel logoLabel = new JLabel(Config.BANK_NAME);
        logoLabel.setFont(Fonts.LOGO);
        logoLabel.setBounds(Paddings.NORMAL * 2 + 96, Paddings.NORMAL + 4, 256, 96);
        add(logoLabel);
    }

    public static Navigator getInstance() {
        return instance;
    }

    public void changePage(Page new_page) {
        changePage(new_page, true);
    }

    public void changePage(Page new_page, boolean beeper) {
        if (page != null) {
            remove(page);
        }
        page = new_page;
        page.setBounds(0, 0, App.getInstance().getWindowWidth(), App.getInstance().getWindowHeight());
        add(page);

        App.getInstance().repaintWindow();

        if (beeper) {
            App.getInstance().sendBeeper(440, 250);
        }
    }

    public void resizePage(int width, int height) {
        page.setBounds(0, 0, width, height);
    }

    public Page getPage() {
        return page;
    }
}
