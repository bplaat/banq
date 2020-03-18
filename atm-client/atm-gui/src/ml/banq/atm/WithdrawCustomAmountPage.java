package ml.banq.atm;

import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JTextField;

public class WithdrawCustomAmountPage extends Page {
    private static final long serialVersionUID = 1;

    private String accountId;
    private String rfid_uid;
    private String pincode;
    private BanqAPI.Account account;

    private JLabel messageLabel;
    private JTextField amountInput;

    public WithdrawCustomAmountPage(String accountId, String rfid_uid, String pincode, BanqAPI.Account account) {
        this.accountId = accountId;
        this.rfid_uid = rfid_uid;
        this.pincode = pincode;
        this.account = account;

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel(Language.getString("withdraw_custom_amount_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);
        add(Box.createVerticalStrut(Paddings.LARGE));

        messageLabel = new JLabel(Language.getString("withdraw_custom_amount_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JPanel amountBox = new JPanel(new FlowLayout(FlowLayout.CENTER, Paddings.NORMAL, 0));
        amountBox.setMaximumSize(new Dimension(App.getInstance().getWindowWidth() / 2, 0));
        add(amountBox);

        JLabel pincodeLabel = new JLabel(Language.getString("withdraw_custom_amount_page_amount_input") + " \u20ac");
        pincodeLabel.setFont(Fonts.NORMAL);
        amountBox.add(pincodeLabel);

        amountInput = new JTextField(4);
        amountInput.setFont(Fonts.NORMAL);
        amountInput.setHorizontalAlignment(JTextField.CENTER);
        amountInput.setMaximumSize(amountInput.getPreferredSize());
        amountBox.add(amountInput);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel backLabel = new JLabel("D. " + Language.getString("withdraw_custom_amount_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new WithdrawAmountPage(accountId, rfid_uid, pincode, account));
        }

        String amount = amountInput.getText();

        if (key.matches("[0-9]")) {
            amountInput.setText(amount + key);
        }

        if (key.equals("*")) {
            amountInput.setText("");
        }

        if (key.equals("#") && amount.length() > 0) {
            int amountNumber = Integer.parseInt(amount);
                App.getInstance().sendBeeper(110, 250);
                messageLabel.setText("Comming soon...");
        }
    }
}
