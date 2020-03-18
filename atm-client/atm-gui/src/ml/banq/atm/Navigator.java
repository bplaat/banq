package ml.banq.atm;

import javax.swing.JLabel;
import javax.swing.JPanel;

public class Navigator extends JPanel {
    private static final long serialVersionUID = 1;

    private static Navigator instance = new Navigator();

    Page page;

    private Navigator() {
        setLayout(null);

        JLabel logo = new JLabel(Utils.loadImage("logo.png", 256, 128));
        logo.setBounds(Paddings.NORMAL, Paddings.NORMAL, 256, 128);
        add(logo);
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
