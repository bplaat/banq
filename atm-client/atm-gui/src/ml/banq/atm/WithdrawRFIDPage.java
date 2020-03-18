package ml.banq.atm;

import java.awt.Component;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

public class WithdrawRFIDPage extends Page {
    private static final long serialVersionUID = 1;

    public WithdrawRFIDPage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel(Language.getString("withdraw_rfid_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel messageLabel = new JLabel(Language.getString("withdraw_rfid_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel backLabel = new JLabel(Language.getString("withdraw_rfid_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new WelcomePage());
        }
    }

    public void onRFIDRead(String account_id, String rfid_uid) {
        Navigator.getInstance().changePage(new WithdrawPincodePage(account_id, rfid_uid));
    }
}
