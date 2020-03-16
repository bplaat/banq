package ml.banq.atm;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLEncoder;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import org.json.JSONArray;
import org.json.JSONObject;

public class BanqAPI {
    public static class Account {
        public static final int TYPE_SAVE = 1;
        public static final int TYPE_PAYMENT = 2;

        private final int id;
        private final String name;
        private final int type;
        private final float amount;
        private final Date created_at;

        public Account(int id, String name, int type, float amount, Date created_at) {
            this.id = id;
            this.name = name;
            this.type = type;
            this.amount = amount;
            this.created_at = created_at;
        }

        public int getId() {
            return id;
        }

        public String getName() {
            return name;
        }

        public int getType() {
            return type;
        }

        public float getAmount() {
            return amount;
        }

        public Date getCreatedAt() {
            return created_at;
        }
    }

    public static class Transaction {
        private final int id;
        private final String name;
        private final String from_account_id;
        private final String to_account_id;
        private final float amount;
        private final Date created_at;

        public Transaction(int id, String name, String from_account_id, String to_account_id, float amount, Date created_at) {
            this.id = id;
            this.name = name;
            this.from_account_id = from_account_id;
            this.to_account_id = to_account_id;
            this.amount = amount;
            this.created_at = created_at;
        }

        public int getId() {
            return id;
        }

        public String getName() {
            return name;
        }

        public String getFromAccountId() {
            return from_account_id;
        }

        public String getToAccountId() {
            return to_account_id;
        }

        public float getAmount() {
            return amount;
        }

        public Date getCreatedAt() {
            return created_at;
        }
    }

    private static BanqAPI instance = new BanqAPI();

    private String key;
    private String session;

    private BanqAPI() {}

    public static BanqAPI getInstance() {
        return instance;
    }

    public String getKey() {
        return instance.key;
    }

    public void setKey(String key) {
        instance.key = key;
    }

    public static int parseAccountId(String account_id) {
        return Integer.parseInt(account_id.substring(8));
    }

    public static String formatAccountId(int account_id) {
        return String.format("SU-BANQ-%08d", account_id);
    }

    public static Date parseDate(String date) throws Exception {
        return new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").parse(date);
    }

    private static JSONObject fetch(String url) throws Exception {
        System.out.println("[INFO] Fetch url: " + url);
        BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(new URL(url).openStream()));
        StringBuilder stringBuilder = new StringBuilder();
        String line;
        while ((line = bufferedReader.readLine()) != null) {
            stringBuilder.append(line);
            stringBuilder.append(System.lineSeparator());
        }
        bufferedReader.close();

        String data = stringBuilder.toString();
        System.out.print("[INFO] Response: " + data);
        if (data.charAt(0) == '{') {
            return new JSONObject(data);
        }

        return null;
    }

    public boolean login(String login, String password) {
        try {
            JSONObject data = fetch(Config.BANQ_API_URL + "/auth/login?key=" + key + "&login=" + URLEncoder.encode(login, "UTF-8") + "&password=" + URLEncoder.encode(password, "UTF-8"));
            if (data.getBoolean("success")) {
                session = data.getString("session");
                return true;
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    public boolean logout() {
        try {
            JSONObject data = fetch(Config.BANQ_API_URL + "/auth/logout?key=" + key + "&session=" + session);
            if (data.getBoolean("success")) {
                session = null;
                return true;
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    public ArrayList<Account> getPaymentAccounts() {
        if (session != null) {
            try {
                JSONObject data = fetch(Config.BANQ_API_URL + "/accounts?key=" + key + "&session=" + session);

                ArrayList<Account> accounts = new ArrayList<Account>();
                JSONArray json_accounts = data.getJSONArray("accounts");
                for (int i = 0; i < json_accounts.length(); i++) {
                    JSONObject json_account = json_accounts.getJSONObject(i);
                    if (json_account.getInt("type") == Account.TYPE_PAYMENT) {
                        accounts.add(new Account(
                            json_account.getInt("id"),
                            json_account.getString("name"),
                            json_account.getInt("type"),
                            json_account.getFloat("amount"),
                            parseDate(json_account.getString("created_at"))
                        ));
                    }
                }

                return accounts;
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
        return null;
    }

    public boolean createCard(String accountId, String rfid_uid, String pincode) {
        if (session != null) {
            try {
                JSONObject data = fetch(Config.BANQ_API_URL + "/cards/create?key=" + key + "&session=" + session + "&name=" + URLEncoder.encode("Card for " + accountId, "UTF-8") + "&account_id=" + String.valueOf(parseAccountId(accountId)) + "&rfid=" + rfid_uid + "&pincode=" + pincode);
                if (data != null) {
                    return true;
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
        return false;
    }

    public Account getAccount(String accountId, String rfid_uid, String pincode) {
        try {
            JSONObject data = fetch(Config.BANQ_API_URL + "/atm/accounts/" + accountId + "?key=" + key + "&rfid=" + rfid_uid + "&pincode=" + pincode);
            if (data.getBoolean("success")) {
                JSONObject json_account = data.getJSONObject("account");
                return new Account(
                    json_account.getInt("id"),
                    json_account.getString("name"),
                    json_account.getInt("type"),
                    json_account.getFloat("amount"),
                    parseDate(json_account.getString("created_at"))
                );
            }
        } catch (Exception e) {
            e.printStackTrace();
        }

        return null;
    }

    public Transaction createTransaction(String fromAccountId, String rfid_uid, String pincode, String name, String toAccountId, float amount) {
        try {
            JSONObject data = fetch(Config.BANQ_API_URL + "/atm/transactions/create?key=" + instance.key + "&name=" + URLEncoder.encode(name, "UTF-8") +
                "&from_account_id=" + fromAccountId + "&to_account_id=" + toAccountId + "&rfid=" + rfid_uid + "&pincode=" + pincode + "&amount=" + amount);
            if (data != null && data.getBoolean("success")) {
                JSONObject json_transaction = data.getJSONObject("transaction");
                return new Transaction(
                    json_transaction.getInt("id"),
                    json_transaction.getString("name"),
                    formatAccountId(json_transaction.getInt("from_account_id")),
                    formatAccountId(json_transaction.getInt("to_account_id")),
                    json_transaction.getFloat("amount"),
                    parseDate(json_transaction.getString("created_at"))
                );
            }
        } catch (Exception e) {
            e.printStackTrace();
        }

        return null;
    }
}
