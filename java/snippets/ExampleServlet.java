package no.partikkel.examples;

import io.jsonwebtoken.Jwts;
import org.apache.commons.codec.binary.Base64;

import javax.servlet.Servlet;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.PrintWriter;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.security.PublicKey;
import java.security.cert.CertificateException;
import java.security.cert.CertificateFactory;
import java.security.cert.X509Certificate;

/**
 * Created by gaute on 28.11.16.
 */
public class ExampleServlet extends HttpServlet
{
    public void doGet(HttpServletRequest request,
                      HttpServletResponse response)
            throws ServletException, IOException
    {
        response.setCharacterEncoding("UTF-8");
        String ticket = request.getParameter("partikkel");
        String path = request.getRequestURI();
        if(ticket!=null && ticketOK(ticket, path)) {
            request.getSession(true).setAttribute(path,"1"); //sesjonsattributt
            response.sendRedirect(path); //valgfri, for å ta bort den stygge ticket-req-param fra url
        }

        final String paidfor = (String) request.getSession(true).getAttribute(path);
        if(paidfor!=null && paidfor.equals("1")){
            response.getWriter().println("You have paid for this!");
        } else {
            response.getWriter().println("<a href=\"https://test.partikkel.io/buy\"> Kjøp artikkel</a> ");
        }

    }


    private boolean ticketOK(String partikkel, String path) {
        String compactJws = new String(Base64.decodeBase64(partikkel));
        PublicKey publicKey = null;
        try {
            publicKey = getPublicKey();
        } catch (Exception e) {
            e.printStackTrace(); //log4j is better
            return false;
        }
        assert Jwts.parser().setSigningKey(publicKey).parseClaimsJws(compactJws).getBody().getSubject().equals("partikkel.io");
        //obs: try/catch over sikkert lurt. Og sjekk path, som ligger i claim "url". Og expiry hvis ikke jjwt gjør det (ikke alle libs gjør det)
        return true;
    }

    private static PublicKey getPublicKey() throws IOException, CertificateException {
        CertificateFactory f = CertificateFactory.getInstance("X.509");
        X509Certificate certificate = (X509Certificate)f.generateCertificate(
                new ByteArrayInputStream(Files.readAllBytes(Paths.get("/var/partikkel/pub.pem"))));
        return certificate.getPublicKey();
    }
}
