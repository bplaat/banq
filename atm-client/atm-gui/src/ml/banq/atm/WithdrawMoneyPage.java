package ml.banq.atm;

import java.awt.Component;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The withdraw money page
public class WithdrawMoneyPage extends Page {
    private static final long serialVersionUID = 1;

    private String accountId;
    private String rfid_uid;
    private String pincode;
    private BanqAPI.Account account;
    private float amount;
    private ArrayList<HashMap<String, Integer>> moneyPares;

    private JLabel messageLabel;

    public WithdrawMoneyPage(String accountId, String rfid_uid, String pincode, BanqAPI.Account account, float amount) {
        this.accountId = accountId;
        this.rfid_uid = rfid_uid;
        this.pincode = pincode;
        this.account = account;
        this.amount = amount;
        moneyPares = MoneyUtils.getMoneyPares((int)amount);

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("withdraw_money_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);
        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message label
        messageLabel = new JLabel(Language.getString("withdraw_money_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create money pares
        for (int i = 0; i < moneyPares.size(); i++) {
            HashMap<String, Integer> moneyPare = moneyPares.get(i);

            String moneyPareString = (i + 1) + ".";
            for (int j = 0; j < Config.ISSUE_AMOUNTS.length; j++) {
                int count = moneyPare.get(String.valueOf(Config.ISSUE_AMOUNTS[j]));
                if (count > 0) {
                    moneyPareString += "    " + MoneyUtils.MONEY_SYMBOL + " " + Config.ISSUE_AMOUNTS[j] + " = " + count + "x";
                }
            }

            JLabel moneyPareLabel = new JLabel(moneyPareString);
            moneyPareLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
            moneyPareLabel.setFont(Fonts.NORMAL);
            add(moneyPareLabel);

            if (i != moneyPares.size() - 1) {
                add(Box.createVerticalStrut(Paddings.NORMAL));
            }
        }

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the back option
        JLabel backLabel = new JLabel("D. " + Language.getString("withdraw_money_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // When back is pressed go back to the amount page
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new WithdrawAmountPage(accountId, rfid_uid, pincode, account));
        }

        // Loop trough the money pares
        for (int i = 0; i < moneyPares.size(); i++) {
            if (key.equals(String.valueOf(i + 1))) {
                // Send the money dispencer commands
                App.getInstance().sendMoney(moneyPares.get(i));

                // Create the transaction via the API
                String name = Language.getString("withdraw_money_page_transaction_prefix") + " " + new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(new Date());
                BanqAPI.Transaction transaction = BanqAPI.getInstance().createTransaction(accountId, rfid_uid, pincode, name, "SU-BANQ-00000001", amount);
                if (transaction != null) {
                    Navigator.getInstance().changePage(new WithdrawReceiptPage(transaction));
                } else {
                    App.getInstance().sendBeeper(110, 250);
                    messageLabel.setText(Language.getString("withdraw_money_page_error"));
                }
            }
        }
    }
}
