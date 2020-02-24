package ml.banq.android;

import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Build;
import android.webkit.CookieManager;
import android.webkit.CookieSyncManager;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.webkit.WebResourceRequest;

public class CustomWebViewClient extends WebViewClient {
    private Context context;

    CustomWebViewClient(Context context) {
        // We save the context of the activity to call some function which needs the context
        this.context = context;
    }

    // Listen when a new page is loaded for older Android versions
    public boolean shouldOverrideUrlLoading(WebView view, String url) {
        return handleUri(Uri.parse(url));
    }

    // Listen when a new page is loaded for newer Android versions
    public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
        return handleUri(request.getUrl());
    }

    // Handle new page loads
    private boolean handleUri(Uri uri) {
        // When we go to a page which is secure and where the hostname is banq.ml load the page as usual
        if (uri.getScheme().equals("https") && uri.getHost().equals("banq.ml")) {
            return false;
        }

        // Or load the page via the corresponding app
        else {
            context.startActivity(new Intent(Intent.ACTION_VIEW, uri));
            return true;
        }
    }

    // Listen when the page is finished by the user
    public void onPageFinished(WebView view, String url) {
        super.onPageFinished(view, url);

        // Force to save the cookies for newer Android versions
        if (Build.VERSION.SDK_INT >= 21) {
            CookieManager.getInstance().flush();
        }

        // Force to save the cookies for older Android versions
        else {
            CookieSyncManager.getInstance().sync();
        }
    }
}
