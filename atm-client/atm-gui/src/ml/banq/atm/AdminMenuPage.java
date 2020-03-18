package ml.banq.atm;

import java.awt.Component;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

public class AdminMenuPage extends Page {
    private static final long serialVersionUID = 1;

    public AdminMenuPage() {
        App.getInstance().showCursor();

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel(Language.getString("admin_menu_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel menu1Label = new JLabel("1. " + Language.getString("admin_menu_page_write"));
        menu1Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu1Label.setFont(Fonts.NORMAL);
        add(menu1Label);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        JLabel menu2Label = new JLabel("D. " + Language.getString("admin_menu_page_back"));
        menu2Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu2Label.setFont(Fonts.NORMAL);
        add(menu2Label);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("1")) {
            Navigator.getInstance().changePage(new AdminWriteLoginPage());
        }

        if (key.equals("D")) {
            if (Config.FULLSCREEN_MODE) {
                App.getInstance().hideCursor();
            }
            Navigator.getInstance().changePage(new WelcomePage());
        }
    }
}
