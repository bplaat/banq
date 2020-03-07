package ml.banq.atm;

import java.awt.Component;
import java.awt.Font;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;

public class AmountPage extends Page {
    private static final long serialVersionUID = 1;

    private int[] defaultAmounts = { 5, 10, 20, 50, 70 };

    public AmountPage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("Select a amount to withdraw");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);
        add(Box.createVerticalStrut(24));

        for (int i = 0; i < defaultAmounts.length; i++) {
            JLabel amountLabel = new JLabel((i + 1) + ". \u20ac" + defaultAmounts[i]);
            amountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
            amountLabel.setFont(Fonts.NORMAL);
            add(amountLabel);
            add(Box.createVerticalStrut(8));
        }

        JLabel customLabel = new JLabel((defaultAmounts.length + 1) + ". Custom");
        customLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        customLabel.setFont(Fonts.NORMAL);
        add(customLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        Navigator.changePage(new DonePage());
    }
}
