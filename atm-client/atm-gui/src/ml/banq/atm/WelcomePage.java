package ml.banq.atm;

import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.GridLayout;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;

public class WelcomePage extends Page {
    private static final long serialVersionUID = 1;

    public WelcomePage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel(Language.getString("welcome_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel messageLabel = new JLabel(Language.getString("welcome_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE * 3));

        JLabel languageLabel = new JLabel(Language.getString("welcome_page_language_input"));
        languageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        languageLabel.setFont(Fonts.NORMAL_BOLD);
        add(languageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JPanel languagesBox = new JPanel(new GridLayout(Config.LANGUAGES.length / 2, Config.LANGUAGES.length / 2, Paddings.LARGE, Paddings.LARGE));
        languagesBox.setMaximumSize(new Dimension(App.getInstance().getWindowWidth() / 3 * 2, 0));
        add(languagesBox);

        for (int i = 0; i < Config.LANGUAGES.length; i++) {
            JPanel languageBox = new JPanel(new FlowLayout(FlowLayout.LEFT, Paddings.LARGE, 0));
            languagesBox.add(languageBox);

            languageBox.add(new JLabel(Utils.loadImage("flag_" + Config.LANGUAGES[i] + ".png", 150, 100)));

            JLabel languageOptionLabel = new JLabel((i + 1) + ". " + Language.getString("language_" + Config.LANGUAGES[i]));
            languageOptionLabel.setFont(Language.getInstance().getLanguage() == Config.LANGUAGES[i] ? Fonts.NORMAL_BOLD : Fonts.NORMAL);
            languageBox.add(languageOptionLabel);
        }

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        for (int i = 0; i < Config.LANGUAGES.length; i++) {
            if (key.equals(String.valueOf(i + 1))) {
                Language.getInstance().changeLanguage(Config.LANGUAGES[i]);
                Navigator.getInstance().changePage(new WelcomePage());
            }
        }

        if (key.equals("#")) {
            Navigator.getInstance().changePage(new WithdrawRFIDPage());
        }
    }

    public void onRFIDRead(String account_id, String rfid_uid) {
        for (int i = 0; i < Config.ADMIN_RFID_UIDS.length; i++) {
            if (rfid_uid.equals(Config.ADMIN_RFID_UIDS[i])) {
                Navigator.getInstance().changePage(new AdminMenuPage());
                return;
            }
        }

        App.getInstance().sendBeeper(880, 250);
        Navigator.getInstance().changePage(new WithdrawPincodePage(account_id, rfid_uid), false);
    }
}
