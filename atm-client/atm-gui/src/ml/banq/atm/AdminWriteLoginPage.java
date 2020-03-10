package ml.banq.atm;

import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.Component;
import java.awt.Font;
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

        add(Box.createVerticalStrut(24));

        JLabel loginLabel = new JLabel("Username or email address: ");
        loginLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        loginLabel.setFont(Fonts.NORMAL);
        add(loginLabel);

        add(Box.createVerticalStrut(16));

        JTextField loginInput = new JTextField(16);
        loginInput.setFont(Fonts.NORMAL);
        loginInput.setHorizontalAlignment(JTextField.CENTER);
        loginInput.setMaximumSize(loginInput.getPreferredSize());
        add(loginInput);

        add(Box.createVerticalStrut(24));

        JLabel passwordLabel = new JLabel("Password: ");
        passwordLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        passwordLabel.setFont(Fonts.NORMAL);
        add(passwordLabel);

        add(Box.createVerticalStrut(16));

        JPasswordField passwordInput = new JPasswordField(16);
        passwordInput.setFont(Fonts.NORMAL);
        passwordInput.setHorizontalAlignment(JPasswordField.CENTER);
        passwordInput.setMaximumSize(passwordInput.getPreferredSize());
        add(passwordInput);

        add(Box.createVerticalStrut(24));

        Box buttonsBox = Box.createHorizontalBox();
        add(buttonsBox);

        JButton loginButton = new JButton("Login");
        loginButton.setFont(Fonts.NORMAL);
        loginButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                if (BanqAPI.login(loginInput.getText(), new String(passwordInput.getPassword()))) {
                    Navigator.changePage(new AdminWriteAccountsPage());
                } else {
                    JOptionPane.showMessageDialog(null, "Incorrect username, email or password");
                }
            }
        });
        buttonsBox.add(loginButton);

        buttonsBox.add(Box.createHorizontalStrut(16));

        JButton backButton = new JButton("Back");
        backButton.setAlignmentX(Component.CENTER_ALIGNMENT);
        backButton.setFont(Fonts.NORMAL);
        backButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                Navigator.changePage(new AdminMenuPage());
            }
        });
        buttonsBox.add(backButton);

        add(Box.createVerticalGlue());
    }
}
