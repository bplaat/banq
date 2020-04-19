package ml.banq.atm;

import java.awt.Component;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The withdraw RFID page
public class WithdrawRFIDPage extends Page {
    private static final long serialVersionUID = 1;

    public WithdrawRFIDPage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("withdraw_rfid_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message label
        JLabel messageLabel = new JLabel(Language.getString("withdraw_rfid_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page back label
        JLabel backLabel = new JLabel(Language.getString("withdraw_rfid_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // When back is pressed go back
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new WelcomePage());
        }
    }

    public void onRFIDRead(String account_id, String rfid_uid) {
        // Else wait for RFID and continue to the pincode page
        App.getInstance().sendBeeper(880, 250);
        Navigator.getInstance().changePage(new WithdrawPincodePage(account_id, rfid_uid), false);
    }
}
