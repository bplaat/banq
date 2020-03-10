package ml.banq.atm;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLEncoder;
import java.util.ArrayList;
import org.json.JSONArray;
import org.json.JSONObject;

public class BanqAPI {
    public static final String API_URL = "http://banq.local/api";
    private static BanqAPI instance = new BanqAPI();

    private String key;
    private String session;
    private String rfid_uid;
    private String accountId;
    private String pincode;

    private BanqAPI() {}

    public static BanqAPI getInstance() {
        return instance;
    }

    public static void setKey(String key) {
        instance.key = key;
    }

    public static String getKey() {
        return instance.key;
    }

    public static void setSession(String session) {
        instance.session = session;
    }

    public static String getSession() {
        return instance.session;
    }

    public static void setRfidUid(String rfid_uid) {
        instance.rfid_uid = rfid_uid;
    }

    public static String getRfidUid() {
        return instance.rfid_uid;
    }

    public static void setAccountId(String accountId) {
        instance.accountId = accountId;
    }

    public static String getAccountId() {
        return instance.accountId;
    }

    public static int parseAccountId(String account_id) {
        return Integer.parseInt(account_id.substring(8));
    }

    public static void setPincode(String pincode) {
        instance.pincode = pincode;
    }

    public static String getPincode() {
        return instance.pincode;
    }

    public static boolean login(String login, String password) {
        try {
            URL url = new URL(API_URL + "/auth/login?login=" + URLEncoder.encode(login, "UTF-8") + "&password=" + URLEncoder.encode(password, "UTF-8"));
            BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(url.openStream()));
            StringBuilder stringBuilder = new StringBuilder();
            String line;
            while ((line = bufferedReader.readLine()) != null) {
                stringBuilder.append(line);
                stringBuilder.append(System.lineSeparator());
            }
            bufferedReader.close();
            String data = stringBuilder.toString();

            if (data.charAt(0) == '{') {
                JSONObject response = new JSONObject(data);

                if (response.getBoolean("success")) {
                    instance.session = response.getString("session");
                    return true;
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    public static boolean logout() {
        try {
            URL url = new URL(API_URL + "/auth/logout?session=" + instance.session);
            BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(url.openStream()));
            StringBuilder stringBuilder = new StringBuilder();
            String line;
            while ((line = bufferedReader.readLine()) != null) {
                stringBuilder.append(line);
                stringBuilder.append(System.lineSeparator());
            }
            bufferedReader.close();
            String data = stringBuilder.toString();

            if (data.charAt(0) == '{') {
                JSONObject response = new JSONObject(data);

                if (response.getBoolean("success")) {
                    instance.session = "";
                    return true;
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    public static class Account {
        public static final int TYPE_SAVE = 1;
        public static final int TYPE_PAYMENT = 2;

        private final int id;
        private final String name;
        private final float amount;

        public Account(int id, String name, float amount) {
            this.id = id;
            this.name = name;
            this.amount = amount;
        }

        public int getId() {
            return id;
        }

        public String getName() {
            return name;
        }

        public float getAmount() {
            return amount;
        }
    }

    public static ArrayList<BanqAPI.Account> getPaymentAccounts() {
        if (!instance.session.equals("")) {
            try {
                URL url = new URL(API_URL + "/accounts?session=" + instance.session);
                BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(url.openStream()));
                StringBuilder stringBuilder = new StringBuilder();
                String line;
                while ((line = bufferedReader.readLine()) != null) {
                    stringBuilder.append(line);
                    stringBuilder.append(System.lineSeparator());
                }
                bufferedReader.close();
                String data = stringBuilder.toString();

                if (data.charAt(0) == '{') {
                    JSONObject response = new JSONObject(data);

                    ArrayList<BanqAPI.Account> accounts = new ArrayList<BanqAPI.Account>();
                    JSONArray json_accounts = response.getJSONArray("accounts");
                    for (int i = 0; i < json_accounts.length(); i++) {
                        JSONObject json_account = json_accounts.getJSONObject(i);
                        if (json_account.getInt("type") == Account.TYPE_PAYMENT) {
                            accounts.add(new BanqAPI.Account(
                                json_account.getInt("id"),
                                json_account.getString("name"),
                                json_account.getFloat("amount")
                            ));
                        }
                    }

                    return accounts;
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
        return null;
    }

    public static boolean createCard() {
        if (!instance.session.equals("")) {
            try {
                URL url = new URL(API_URL + "/cards/create?session=" + instance.session + "&name=" + URLEncoder.encode("Card for " + instance.accountId, "UTF-8") + "&account_id=" + URLEncoder.encode(String.valueOf(parseAccountId(instance.accountId)), "UTF-8") + "&rfid=" + URLEncoder.encode(instance.rfid_uid, "UTF-8") + "&pincode=" + URLEncoder.encode(instance.pincode, "UTF-8"));
                BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(url.openStream()));
                StringBuilder stringBuilder = new StringBuilder();
                String line;
                while ((line = bufferedReader.readLine()) != null) {
                    stringBuilder.append(line);
                    stringBuilder.append(System.lineSeparator());
                }
                bufferedReader.close();
                String data = stringBuilder.toString();

                if (data.charAt(0) == '{') {
                    JSONObject response = new JSONObject(data);

                    return true;
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
        return false;
    }
}
