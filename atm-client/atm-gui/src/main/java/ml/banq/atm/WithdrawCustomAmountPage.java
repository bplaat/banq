package ml.banq.atm;

import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JTextField;

// The withdraw custom amount page
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

        // Create the page title label
        JLabel titleLabel = new JLabel(Language.getString("withdraw_custom_amount_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);
        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message label
        messageLabel = new JLabel(Language.getString("withdraw_custom_amount_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page amount box
        JPanel amountBox = new JPanel(new FlowLayout(FlowLayout.CENTER, Paddings.NORMAL, 0));
        amountBox.setMaximumSize(new Dimension(App.getInstance().getWindowWidth() / 2, 0));
        add(amountBox);

        // Create the page amount input label
        JLabel amountLabel = new JLabel(Language.getString("withdraw_custom_amount_page_amount_input") + " " + MoneyUtils.getMoneySymbol());
        amountLabel.setFont(Fonts.NORMAL);
        amountBox.add(amountLabel);

        // Create the amount input field
        amountInput = new JTextField(4);
        amountInput.setFont(Fonts.NORMAL);
        amountInput.setHorizontalAlignment(JTextField.CENTER);
        amountInput.setMaximumSize(amountInput.getPreferredSize());
        amountBox.add(amountInput);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the account menu option label
        JLabel accountLabel = new JLabel("B. " + Language.getString("withdraw_custom_amount_page_account"));
        accountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        accountLabel.setFont(Fonts.NORMAL);
        add(accountLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the back label menu option
        JLabel backLabel = new JLabel("D. " + Language.getString("withdraw_custom_amount_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // The amount edit stuff
        String amount = amountInput.getText();

        if (key.matches("[0-9]")) {
            amountInput.setText(amount + key);
        }

        if (key.equals("*")) {
            amountInput.setText("");
        }

        if (key.equals("#") && amount.length() > 0) {
            // Check if the amount is a multiple of five
            float amountFloat = Float.parseFloat(amount);
            if (amountFloat % 5 == 0) {
                if (account.getAmount() - amountFloat >= 0) {
                    Navigator.getInstance().changePage(new WithdrawMoneyPage(accountId, rfid_uid, pincode, account, amountFloat));
                } else {
                    App.getInstance().sendBeeper(110, 250);
                    messageLabel.setFont(Fonts.NORMAL_BOLD);
                    messageLabel.setText(Language.getString("withdraw_amount_page_not_enough"));
                }
            }

            // If not show a error message
            else {
                App.getInstance().sendBeeper(110, 250);
                messageLabel.setFont(Fonts.NORMAL_BOLD);
                messageLabel.setText(Language.getString("withdraw_custom_amount_page_error"));
                amountInput.setText("");
            }
        }

        // Go back to the account page when the account menu option is selected
        if (key.equals("B")) {
            Navigator.getInstance().changePage(new WithdrawAccountPage(accountId, rfid_uid, pincode, account));
        }

        // When back is pressed go to the amount page
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new WithdrawAmountPage(accountId, rfid_uid, pincode, account));
        }
    }
}
