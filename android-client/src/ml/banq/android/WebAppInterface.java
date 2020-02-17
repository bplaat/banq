package ml.banq.android;

import android.content.Context;
import android.content.Intent;
import android.webkit.JavascriptInterface;

public class WebAppInterface {
    Context context;

    WebAppInterface(Context context) {
        this.context = context;
    }

    @JavascriptInterface
    public void share(String text) {
        Intent intent = new Intent();
        intent.setAction(Intent.ACTION_SEND);
        intent.putExtra(Intent.EXTRA_TEXT, text);
        intent.setType("text/plain");
        context.startActivity(Intent.createChooser(intent, null));
    }
}
