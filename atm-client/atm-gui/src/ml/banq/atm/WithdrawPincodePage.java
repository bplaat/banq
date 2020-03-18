package ml.banq.atm;

import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JPasswordField;

public class WithdrawPincodePage extends Page {
    private static final long serialVersionUID = 1;

    private String accountId;
    private String rfid_uid;

    private JLabel messageLabel;
    private JPasswordField pincodeInput;

    public WithdrawPincodePage(String accountId, String rfid_uid) {
        this.accountId = accountId;
        this.rfid_uid = rfid_uid;

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel(Language.getString("withdraw_pincode_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        messageLabel = new JLabel(Language.getString("withdraw_pincode_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JPanel pincodeBox = new JPanel(new FlowLayout(FlowLayout.CENTER, Paddings.NORMAL, 0));
        pincodeBox.setMaximumSize(new Dimension(App.getInstance().getWindowWidth() / 2, 0));
        add(pincodeBox);

        JLabel pincodeLabel = new JLabel(Language.getString("withdraw_pincode_page_pincode_input"));
        pincodeLabel.setFont(Fonts.NORMAL);
        pincodeBox.add(pincodeLabel);

        pincodeInput = new JPasswordField(4);
        pincodeInput.setFont(Fonts.NORMAL);
        pincodeInput.setHorizontalAlignment(JPasswordField.CENTER);
        pincodeInput.setMaximumSize(pincodeInput.getPreferredSize());
        pincodeBox.add(pincodeInput);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel backLabel = new JLabel(Language.getString("withdraw_pincode_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new WelcomePage());
        }

        String pincode = new String(pincodeInput.getPassword());

        if (key.matches("[0-9]") && pincode.length() < 4) {
            pincodeInput.setText(pincode + key);
        }

        if (key.equals("*")) {
            pincodeInput.setText("");
        }

        if (pincode.length() == 4 && key.equals("#")) {
            BanqAPI.Account account = BanqAPI.getInstance().getAccount(accountId, rfid_uid, pincode);
            if (account != null) {
                App.getInstance().sendBeeper(880, 250);
                Navigator.getInstance().changePage(new WithdrawAccountPage(accountId, rfid_uid, pincode, account));
            } else {
                App.getInstance().sendBeeper(110, 250);
                messageLabel.setText(Language.getString("withdraw_pincode_message_error"));
                pincodeInput.setText("");
            }
        }
    }
}
