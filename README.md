# Hallandsspexet website projects

WP customizations for hallandsspexet webpage

## Custom plugins

### Hallandsspexet food

### Hallandsspexet users

### Hallandsspexet tab

## Third party plugins

* Easy WP SMTP
* Events Manager
* WordPress Access Control

## TODO

1. (DONE) Medlemsregistrering (sköts av admin).

2.a (DONE) Medlemslistan är synlig för alla spexare, dock ej för allmänheten.
   Namn
   email
   telefonnummer
   utskott (olika listor)

2.b Medlemslista styling

3.a (DONE) Spexare har nota, kredit för fester, baren och föreställningar.
	Fester: Auto
    Föreställningar: Atuo
	Bar: Manuell
	Övrigt: Manuell

3.b Spexare kan se sin egen nota.

3.c (DONE) Admins kan se och lägga till kredit på alla spexares notor.

4. (DONE) Admins kan se detaljerad information om spexarna
   Namn
   personnummer
   email
   telefonnummer
   adress
   stad
   postnummer
   utskott (ta bort veteraner, Ekonomi, Övrigt, festeriet) (Lägg till syeri)

5. (DONE) Generera deltagarlistor för fester med matpreferenser. Lägg till column i booking table
   https://plugins.trac.wordpress.org/browser/events-manager/trunk/classes/em-bookings-table.php

7. Reservera biljetter. Manuell betalningsverifiering (admin).
   1. (DONE) Skapa föreställningar (tid, lokal, antal, biljetter, pris osv)
   2. Credit spexare vid köp av biljett.
   3. (DONE) Beställa biljetter.
   4. (DONE) Få ett mail med info vid beställning.
   5. (DONE) Inloggad spexare kan köpa biljetter på kredit.
   6. (DONE) Lägg till 'förbrukad' action i bookings table, filter 'em_bookings_table_cols_col_action'
   7. (DONE) Fixa mail med filter('em_booking_email_messages')
   8. (DONE) Lägg till ID på alla bokningar av föreställningar, skicka ID't i mailet vid 'approve'.
   9. (DONE) Lägg till sök på ID i bookings.

8. E-tickets visa/mastercard. (Om inte detta är möjligt behöver vi ett
   automatiskt system som skickar betalningsuppgifter till kunderna.)
   1. Lägg till betalning via tex braintree.

9. Kontaktuppgifter

10. Årets show

11. Manus

12. Om spexet (historia)

13. Bildgalleri

14. (DONE) Extern kalender. Endast synlig för spexare. Innehåller länkar till
   Facebook-events.
