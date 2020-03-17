package ml.banq.atm;

import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JPasswordField;
import javax.swing.JOptionPane;
import javax.swing.JTextField;

public class AdminWriteLoginPage extends Page {
    private static final long serialVersionUID = 1;

    public AdminWriteLoginPage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("Login to your Banq account");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel loginLabel = new JLabel("Username or email address: ");
        loginLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        loginLabel.setFont(Fonts.NORMAL);
        add(loginLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        JTextField loginInput = new JTextField(16);
        loginInput.setFont(Fonts.NORMAL);
        loginInput.setHorizontalAlignment(JTextField.CENTER);
        loginInput.setMaximumSize(loginInput.getPreferredSize());
        add(loginInput);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel passwordLabel = new JLabel("Password: ");
        passwordLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        passwordLabel.setFont(Fonts.NORMAL);
        add(passwordLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        JPasswordField passwordInput = new JPasswordField(16);
        passwordInput.setFont(Fonts.NORMAL);
        passwordInput.setHorizontalAlignment(JPasswordField.CENTER);
        passwordInput.setMaximumSize(passwordInput.getPreferredSize());
        add(passwordInput);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JPanel buttonsBox = new JPanel(new FlowLayout(FlowLayout.CENTER, Paddings.NORMAL, 0));
        buttonsBox.setMaximumSize(new Dimension(App.getInstance().getWindowWidth() / 2, Fonts.NORMAL.getSize() * 2));
        add(buttonsBox);

        JButton loginButton = new JButton("Login");
        loginButton.setFont(Fonts.NORMAL);
        loginButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                if (BanqAPI.getInstance().login(loginInput.getText(), new String(passwordInput.getPassword()))) {
                    App.getInstance().sendBeeper(880, 250);
                    Navigator.getInstance().changePage(new AdminWriteAccountsPage());
                } else {
                    App.getInstance().sendBeeper(110, 250);
                    JOptionPane.showMessageDialog(null, "Incorrect username, email or password");
                }
            }
        });
        buttonsBox.add(loginButton);

        JButton backButton = new JButton("Back");
        backButton.setAlignmentX(Component.CENTER_ALIGNMENT);
        backButton.setFont(Fonts.NORMAL);
        backButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                Navigator.getInstance().changePage(new AdminMenuPage());
            }
        });
        buttonsBox.add(backButton);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new AdminMenuPage());
        }
    }
}
