package no.partikkel.examples;

import io.jsonwebtoken.Claims;
import io.jsonwebtoken.Jws;
import io.jsonwebtoken.Jwts;
import org.apache.commons.codec.binary.Base64;
import org.apache.commons.codec.binary.StringUtils;

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
import java.util.Date;

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
        final Jws<Claims> claims = Jwts.parser().setSigningKey(publicKey).parseClaimsJws(compactJws);
        final Claims claimsBody = claims.getBody();
        String surl = claimsBody.get("url",String.class);
        if(!"partikkel.io".equals(claimsBody.getIssuer()))return false;
        if(!new Date().before(claimsBody.getExpiration()))return false;
        if(surl.indexOf(path)<0)return false; //check article paid for is this one  - and log these errors maybe..
        return true;
    }

    private static PublicKey getPublicKey() throws IOException, CertificateException {
        CertificateFactory f = CertificateFactory.getInstance("X.509");
        X509Certificate certificate = (X509Certificate)f.generateCertificate(
                new ByteArrayInputStream(Files.readAllBytes(Paths.get("/var/partikkel/pub.pem"))));
        return certificate.getPublicKey();
    }
}
