package ml.banq.atm;

import java.awt.Component;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The withdraw amount page
public class WithdrawAmountPage extends Page {
    private static final long serialVersionUID = 1;

    private String accountId;
    private String rfid_uid;
    private String pincode;
    private BanqAPI.Account account;

    private JLabel messageLabel;

    public WithdrawAmountPage(String accountId, String rfid_uid, String pincode, BanqAPI.Account account) {
        this.accountId = accountId;
        this.rfid_uid = rfid_uid;
        this.pincode = pincode;
        this.account = account;

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("withdraw_amount_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);
        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message label
        messageLabel = new JLabel(Language.getString("withdraw_amount_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the amount options
        for (int i = 0; i < Config.DEFAULT_AMOUNTS.length; i++) {
            JLabel amountLabel = new JLabel((i + 1) + ". \u20bd " + Config.DEFAULT_AMOUNTS[i]);
            amountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
            amountLabel.setFont(Fonts.NORMAL);
            add(amountLabel);
            add(Box.createVerticalStrut(Paddings.NORMAL));
        }

        // Create the custom amount option
        JLabel customLabel = new JLabel((Config.DEFAULT_AMOUNTS.length + 1) + ". " + Language.getString("withdraw_amount_page_custom"));
        customLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        customLabel.setFont(Fonts.NORMAL);
        add(customLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the back option
        JLabel backLabel = new JLabel("D. " + Language.getString("withdraw_amount_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // When back is pressed go back
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new WithdrawAccountPage(accountId, rfid_uid, pincode, account));
        }

        // Check if one of the amount is pressed and go to the money page
        for (int i = 0; i < Config.DEFAULT_AMOUNTS.length; i++) {
            if (key.equals(String.valueOf(i + 1))) {
                if (account.getAmount() - Config.DEFAULT_AMOUNTS[i] >= 0) {
                    Navigator.getInstance().changePage(new WithdrawMoneyPage(accountId, rfid_uid, pincode, account, Config.DEFAULT_AMOUNTS[i]));
                } else {
                    App.getInstance().sendBeeper(110, 250);
                    messageLabel.setText(Language.getString("withdraw_amount_page_not_enough"));
                }
            }
        }

        // When the custom amount is selected go to the page
        if (key.equals(String.valueOf(Config.DEFAULT_AMOUNTS.length + 1))) {
            Navigator.getInstance().changePage(new WithdrawCustomAmountPage(accountId, rfid_uid, pincode, account));
        }
    }
}
