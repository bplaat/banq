package ml.banq.atm;

import java.awt.Component;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The Admin menu page class
public class AdminMenuPage extends Page {
    private static final long serialVersionUID = 1;

    public AdminMenuPage() {
        // Show the cursor when in fullscreen mode in all the Admin pages
        if (Config.FULLSCREEN_MODE) {
            App.getInstance().showCursor();
        }

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("admin_menu_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the first menu option
        JLabel menu1Label = new JLabel("1. " + Language.getString("admin_menu_page_write"));
        menu1Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu1Label.setFont(Fonts.NORMAL);
        add(menu1Label);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the second menu / back option
        JLabel menu2Label = new JLabel("D. " + Language.getString("admin_menu_page_back"));
        menu2Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu2Label.setFont(Fonts.NORMAL);
        add(menu2Label);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // When menu option 1 is selected go to the admin write login page
        if (key.equals("1")) {
            Navigator.getInstance().changePage(new AdminWriteLoginPage());
        }

        // When menu option 2 / back is selected go back to the welcome page and hide the cursor again
        if (key.equals("D")) {
            if (Config.FULLSCREEN_MODE) {
                App.getInstance().hideCursor();
            }
            Navigator.getInstance().changePage(new WelcomePage());
        }
    }
}