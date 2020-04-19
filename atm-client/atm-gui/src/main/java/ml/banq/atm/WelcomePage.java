package ml.banq.atm;

import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.GridLayout;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;

// The welcome page
public class WelcomePage extends Page {
    private static final long serialVersionUID = 1;

    public WelcomePage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("welcome_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message label
        JLabel messageLabel = new JLabel(Language.getString("welcome_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page controls label
        JLabel controlsLabel = new JLabel(Language.getString("welcome_page_controls"));
        controlsLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        controlsLabel.setFont(Fonts.NORMAL);
        add(controlsLabel);

        add(Box.createVerticalStrut(Paddings.LARGE * 2));

        // Create the page language message label
        JLabel languageLabel = new JLabel(Language.getString("welcome_page_language_input"));
        languageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        languageLabel.setFont(Fonts.NORMAL_BOLD);
        add(languageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page languages box
        JPanel languagesBox = new JPanel(new GridLayout(0, Config.LANGUAGES.length / 2, Paddings.LARGE, Paddings.LARGE));
        languagesBox.setMaximumSize(new Dimension(App.getInstance().getWindowWidth() / 3 * 2, 0));
        add(languagesBox);

        // Create the language menu options
        for (int i = 0; i < Config.LANGUAGES.length; i++) {
            JPanel languageBox = new JPanel(new FlowLayout(FlowLayout.LEFT, Paddings.LARGE, 0));
            languagesBox.add(languageBox);

            languageBox.add(new JLabel(ImageUtils.loadImage("flag_" + Config.LANGUAGES[i][0] + ".png", 150, 100)));

            JLabel languageOptionLabel = new JLabel((i + 1) + ". " + Config.LANGUAGES[i][1]);
            languageOptionLabel.setFont(Language.getInstance().getLanguage() == Config.LANGUAGES[i][0] ? Fonts.NORMAL_BOLD : Fonts.NORMAL);
            languageBox.add(languageOptionLabel);
        }

        add(Box.createVerticalStrut(Paddings.LARGE * 2));

        // Create the device location label
        JLabel locationLabel = new JLabel(Language.getString("welcome_page_location") + " " + Settings.getInstance().getItem("location", Config.DEFAULT_LOCATION));
        locationLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        locationLabel.setFont(Fonts.SMALL);
        add(locationLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // When a language menu option is selected change the app language
        for (int i = 0; i < Config.LANGUAGES.length; i++) {
            if (key.equals(String.valueOf(i + 1)) && !Config.LANGUAGES[i][0].equals(Language.getInstance().getLanguage())) {
                Language.getInstance().changeLanguage(Config.LANGUAGES[i][0]);
                Navigator.getInstance().changePage(new WelcomePage());
            }
        }

        // When the # is pressed continue to the RFID read page
        if (key.equals("#")) {
            Navigator.getInstance().changePage(new WithdrawRFIDPage());
        }
    }

    public void onRFIDRead(String account_id, String rfid_uid) {
        // When a admin RFID card is scanned go to the admin menu page
        for (int i = 0; i < Config.ADMIN_RFID_UIDS.length; i++) {
            if (rfid_uid.equals(Config.ADMIN_RFID_UIDS[i])) {
                Navigator.getInstance().changePage(new AdminMenuPage());
                return;
            }
        }

        // Else do a short cut and go direct to the pincode page
        App.getInstance().sendBeeper(880, 250);
        Navigator.getInstance().changePage(new WithdrawPincodePage(account_id, rfid_uid), false);
    }
}
