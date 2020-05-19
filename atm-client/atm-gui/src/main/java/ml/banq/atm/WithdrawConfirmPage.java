package ml.banq.atm;

import java.awt.Component;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The withdraw confirm page
public class WithdrawConfirmPage extends Page {
    private static final long serialVersionUID = 1;

    private String accountId;
    private String rfid_uid;
    private String pincode;
    private BanqAPI.Account account;
    private float amount;
    private HashMap<String, Integer> moneyPare;

    private JLabel messageLabel;

    public WithdrawConfirmPage(String accountId, String rfid_uid, String pincode, BanqAPI.Account account, float amount, HashMap<String, Integer> moneyPare) {
        this.accountId = accountId;
        this.rfid_uid = rfid_uid;
        this.pincode = pincode;
        this.account = account;
        this.amount = amount;
        this.moneyPare = moneyPare;

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("withdraw_confirm_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message label
        String moneyPareString = "";
        for (int j = 0; j < Config.ISSUE_AMOUNTS.length; j++) {
            int count = moneyPare.get(String.valueOf(Config.ISSUE_AMOUNTS[j]));
            if (count > 0) {
                moneyPareString += "    " + MoneyUtils.getMoneySymbol() + " " + Config.ISSUE_AMOUNTS[j] + " = " + count + "x";
            }
        }

        messageLabel = new JLabel("1. " + Language.getString("withdraw_confirm_page_message") + " " + moneyPareString);
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the account menu option label
        JLabel accountLabel = new JLabel("B. " + Language.getString("withdraw_money_page_account"));
        accountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        accountLabel.setFont(Fonts.NORMAL);
        add(accountLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the back option
        JLabel backLabel = new JLabel("D. " + Language.getString("withdraw_confirm_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // When the first option is selected
        if (key.equals("1")) {
            // Create the transaction via the API
            String name = Language.getString("withdraw_confirm_page_transaction_prefix") + " " + new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(new Date());
            BanqAPI.Transaction transaction = BanqAPI.getInstance().createTransaction(accountId, rfid_uid, pincode, name, "SU-BANQ-00000001", amount);
            if (transaction != null) {
                App.getInstance().sendBeeper(880, 250);
                Navigator.getInstance().changePage(new WithdrawReceiptPage(transaction, moneyPare), false);
            } else {
                App.getInstance().sendBeeper(110, 250);
                messageLabel.setText(Language.getString("withdraw_confirm_page_error"));
            }
        }

        // Go back to the account page when the account menu option is selected
        if (key.equals("B")) {
            Navigator.getInstance().changePage(new WithdrawAccountPage(accountId, rfid_uid, pincode, account));
        }

        // When back is pressed go back to the amount page
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new WithdrawMoneyPage(accountId, rfid_uid, pincode, account, amount));
        }
    }
}
