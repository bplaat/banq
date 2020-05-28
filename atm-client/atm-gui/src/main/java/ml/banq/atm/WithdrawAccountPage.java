package ml.banq.atm;

import java.awt.Component;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The withdraw account page
public class WithdrawAccountPage extends Page {
    private static final long serialVersionUID = 1;

    private String accountId;
    private String rfid_uid;
    private String pincode;
    private BanqAPI.Account account;
    private JLabel messageLabel;

    public WithdrawAccountPage(String accountId, String rfid_uid, String pincode, BanqAPI.Account account) {
        this.accountId = accountId;
        this.rfid_uid = rfid_uid;
        this.pincode = pincode;
        this.account = account;

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("withdraw_account_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message
        messageLabel = new JLabel(Language.getString("withdraw_account_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the first menu option label
        JLabel menu1Label = new JLabel("1. " + Language.getString("withdraw_account_page_balance"));
        menu1Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu1Label.setFont(Fonts.NORMAL);
        add(menu1Label);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the second menu option label
        JLabel menu2Label = new JLabel("2. " + Language.getString("withdraw_account_page_withdraw"));
        menu2Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu2Label.setFont(Fonts.NORMAL);
        add(menu2Label);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the third menu option label
        JLabel menu3Label = new JLabel("3. " + Language.getString("withdraw_account_page_quick_withdraw"));
        menu3Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu3Label.setFont(Fonts.NORMAL);
        add(menu3Label);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the account menu option label
        JLabel accountLabel = new JLabel("B. " + Language.getString("withdraw_account_page_account"));
        accountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        accountLabel.setFont(Fonts.NORMAL);
        add(accountLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the back menu option label
        JLabel backLabel = new JLabel("D. " + Language.getString("withdraw_account_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // Go to the widthdraw balance page when the first menu option is selected
        if (key.equals("1")) {
            Navigator.getInstance().changePage(new WithdrawBalancePage(accountId, rfid_uid, pincode, account));
        }

        // Go to the withdraw amount page when the second menu option is selected
        if (key.equals("2")) {
            Navigator.getInstance().changePage(new WithdrawAmountPage(accountId, rfid_uid, pincode, account));
        }

        // Withdraw 70 as amount when the third menu option is selected
        if (key.equals("3")) {
            int amount = 70;

            // Check account amount
            if (account.getAmount() - amount >= 0) {
                // Check if a money pare if available
                ArrayList<HashMap<String, Integer>> moneyPares = MoneyUtils.getMoneyPares(amount);
                if (moneyPares.size() != 0) {
                    // Select the last money pare
                    HashMap<String, Integer> moneyPare = moneyPares.get(moneyPares.size() - 1);

                    // Create the transaction via the API
                    String name = Language.getString("withdraw_confirm_page_transaction_prefix") + " " + new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(new Date());
                    BanqAPI.Transaction transaction = BanqAPI.getInstance().createTransaction(accountId, rfid_uid, pincode, name, "SO-BANQ-00000001", amount);
                    if (transaction != null) {
                        App.getInstance().sendBeeper(880, 250);
                        Navigator.getInstance().changePage(new WithdrawMoneyWaitPage(transaction, moneyPare, false), false);
                    } else {
                        App.getInstance().sendBeeper(110, 250);
                        messageLabel.setFont(Fonts.NORMAL_BOLD);
                        messageLabel.setText(Language.getString("withdraw_account_page_quick_error"));
                    }
                }
                else {
                    App.getInstance().sendBeeper(110, 250);
                    messageLabel.setFont(Fonts.NORMAL_BOLD);
                    messageLabel.setText(Language.getString("withdraw_account_page_quick_no_pares"));
                }
            } else {
                App.getInstance().sendBeeper(110, 250);
                messageLabel.setFont(Fonts.NORMAL_BOLD);
                messageLabel.setText(Language.getString("withdraw_account_page_quick_not_enough"));
            }
        }

        // Go back to the account page when the account menu option is selected
        if (key.equals("B")) {
            Navigator.getInstance().changePage(new WithdrawAccountPage(accountId, rfid_uid, pincode, account));
        }

        // Go back to the welcome page when the back menu option is selected
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new WelcomePage());
        }
    }
}
