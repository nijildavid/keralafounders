-- Kerala Founders: add Denmark, Spain, Austria, Finland, Portugal companies (batch 4)
-- Safe to run regardless of production's current auto-increment state:
-- companies are inserted without explicit ids, and founders are linked
-- back to their company by slug (unique) rather than a hardcoded id.

INSERT INTO companies (slug, name, website, industry, size, founded_year, country, city, location, description, status, verified) VALUES
('atami-sushi-restaurant-denmark','Atami Sushi Restaurant Denmark',NULL,'Restaurant / Food & Beverage',NULL,NULL,'Denmark','Odense','Odense, Denmark','Restaurant / Food & Beverage company founded by Tony Mathew in Odense, Denmark.','pending',0),
('little-india-boadilla','Little India Boadilla','https://restaurantelittleindia.com/','Restaurant / Indian / South Indian',NULL,NULL,'Spain','Boadilla del Monte','Boadilla del Monte, Spain','Restaurant / Indian / South Indian company founded by Jeejo Puthenveettil George in Boadilla del Monte, Spain.','pending',0),
('indian-aderezo','Indian Aderezo','https://indianaderezo.com/','Restaurant / Indian / South Indian',NULL,NULL,'Spain','Valladolid','Valladolid, Spain','Restaurant / Indian / South Indian company founded by Shiju Ambazhakkadan Devassykutty in Valladolid, Spain.','pending',0),
('prosi-pallikunnel','PROSI Pallikunnel KG / PROSI Exotic Supermarket','https://www.prosi.at/','Kerala Grocery / South Asian Food Retail',NULL,NULL,'Austria','Vienna','Vienna, Austria','Kerala Grocery / South Asian Food Retail company founded by Dr. Augustin (Prince) Pallikunnel in Vienna, Austria.','approved',0),
('sonnentag-kerala-ayurveda-shop','Sonnentag, Kerala Ayurveda Shop GmbH','https://www.keralaayurvedashop.at/','Wellness / Ayurveda / Consumer Health',NULL,NULL,'Austria','Vienna','Vienna, Austria','Wellness / Ayurveda / Consumer Health company founded by Lal Karinkada Pushkaran, Julia Karinkada in Vienna, Austria.','approved',0),
('karinkada-ayurveda','Karinkada Ayurveda GmbH','https://www.karinkadaayurveda.at/','Manufacturing / Ayurveda / Consumer Products',NULL,NULL,'Austria','Purkersdorf','Purkersdorf, Austria','Manufacturing / Ayurveda / Consumer Products company founded by Lal Karinkada Pushkaran, Julia Karinkada in Purkersdorf, Austria.','approved',0),
('thinnan','thinnan','https://www.thinnan.com/','Food Technology',NULL,NULL,'Finland','Helsinki','Helsinki, Finland','Food Technology company founded by Annu Mathew, Kevin Jacob, Vishnu Aravind in Helsinki, Finland.','approved',0),
('venturevillage','VentureVillage Oy','https://venturevillage.world/','Education / Research Facilitation',NULL,NULL,'Finland','Espoo','Espoo, Finland','Education / Research Facilitation company founded by Unnikrishnan S. Kurup, Dr. Anup Jinadevan in Espoo, Finland.','approved',0),
('gravito','Gravito Oy','https://gravito.fi/','AI / Software / Digital',NULL,NULL,'Finland','Espoo','Espoo, Finland','AI / Software / Digital company founded by Unnikrishnan S. Kurup, Antti Kuronen in Espoo, Finland.','approved',0),
('saraswati','Saraswati','https://www.saraswati.fi/','Healthcare / Ayurveda / Wellness',NULL,NULL,'Finland','Helsinki','Helsinki, Finland','Healthcare / Ayurveda / Wellness company founded by Terry Thomas in Helsinki, Finland.','approved',0),
('kerala-restaurant-lisbon','Kerala Restaurant','https://keralarestaurant.pt/','Restaurant / Kerala Food',NULL,NULL,'Portugal','Lisbon','Lisbon, Portugal','Restaurant / Kerala Food company founded by Vijeesh Rajan, Thrinisha Mohandas in Lisbon, Portugal.','approved',0),
('costa-do-malabar','Costa do Malabar','https://www.costadomalabar.com/','Restaurant / Kerala Food',NULL,NULL,'Portugal','Lisbon','Lisbon, Portugal','Restaurant / Kerala Food company founded by Sunilkumar Bhaskaran in Lisbon, Portugal.','approved',0),
('swaad','Swaad','https://swaadporto.com/','Restaurant / South Indian / Kerala Food',NULL,NULL,'Portugal','Porto','Porto, Portugal','Restaurant / South Indian / Kerala Food company founded by Rishi Jacob in Porto, Portugal.','approved',0),
('curry-clubs','Curry Clubs','https://www.curryclubs.com/','Restaurant / Indian / South Indian',NULL,NULL,'Portugal','Albufeira','Albufeira, Portugal','Restaurant / Indian / South Indian company founded by Alex, Mary in Albufeira, Portugal.','approved',0),
('hello-coop','Hellō','https://hello.coop/','AI / Software / Digital',NULL,NULL,'Portugal','Lisbon','Lisbon, Portugal','AI / Software / Digital company founded by Rohan Harikumar in Lisbon, Portugal.','pending',0);

INSERT INTO founders (company_id, name, email, linkedin, show_email) VALUES
((SELECT id FROM companies WHERE slug='atami-sushi-restaurant-denmark'),'Tony Mathew',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='little-india-boadilla'),'Jeejo Puthenveettil George',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='indian-aderezo'),'Shiju Ambazhakkadan Devassykutty',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='prosi-pallikunnel'),'Dr. Augustin (Prince) Pallikunnel',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='sonnentag-kerala-ayurveda-shop'),'Lal Karinkada Pushkaran',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='sonnentag-kerala-ayurveda-shop'),'Julia Karinkada',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='karinkada-ayurveda'),'Lal Karinkada Pushkaran',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='karinkada-ayurveda'),'Julia Karinkada',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='thinnan'),'Annu Mathew',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='thinnan'),'Kevin Jacob',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='thinnan'),'Vishnu Aravind',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='venturevillage'),'Unnikrishnan S. Kurup',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='venturevillage'),'Dr. Anup Jinadevan',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='gravito'),'Unnikrishnan S. Kurup',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='gravito'),'Antti Kuronen',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='saraswati'),'Terry Thomas',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kerala-restaurant-lisbon'),'Vijeesh Rajan',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kerala-restaurant-lisbon'),'Thrinisha Mohandas',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='costa-do-malabar'),'Sunilkumar Bhaskaran',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='swaad'),'Rishi Jacob',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='curry-clubs'),'Alex',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='curry-clubs'),'Mary',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='hello-coop'),'Rohan Harikumar',NULL,NULL,0);
