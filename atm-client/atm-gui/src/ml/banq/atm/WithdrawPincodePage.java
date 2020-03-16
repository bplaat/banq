package ml.banq.atm;

import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.Font;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JPasswordField;

public class WithdrawPincodePage extends Page {
    private static final long serialVersionUID = 1;

    private String accountId;
    private String rfid_uid;

    private JLabel message1Label;
    private JPasswordField pincodeInput;

    public WithdrawPincodePage(String accountId, String rfid_uid) {
        this.accountId = accountId;
        this.rfid_uid = rfid_uid;

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("Enter your pincode");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        message1Label = new JLabel("Enter your pincode press '#' when you are finished");
        message1Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        message1Label.setFont(Fonts.NORMAL);
        add(message1Label);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        JLabel message2Label = new JLabel("Press '*' to clear the pincode");
        message2Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        message2Label.setFont(Fonts.NORMAL);
        add(message2Label);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JPanel pincodeBox = new JPanel(new FlowLayout(FlowLayout.CENTER, Paddings.NORMAL, 0));
        pincodeBox.setMaximumSize(new Dimension(320, 64));
        add(pincodeBox);

        JLabel pincodeLabel = new JLabel("Pincode: ");
        pincodeLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        pincodeLabel.setFont(Fonts.NORMAL);
        pincodeBox.add(pincodeLabel);

        pincodeInput = new JPasswordField(4);
        pincodeInput.setFont(Fonts.NORMAL);
        pincodeInput.setHorizontalAlignment(JPasswordField.CENTER);
        pincodeInput.setMaximumSize(pincodeInput.getPreferredSize());
        pincodeBox.add(pincodeInput);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel backLabel = new JLabel("Press the 'D' key to go back to the welcome page");
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
                message1Label.setText("The pincode is wrong or your card is blocked");
                pincodeInput.setText("");
            }
        }
    }
}
