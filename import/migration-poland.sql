-- Kerala Founders: add Poland companies (batch 8)
-- Safe to run regardless of production's current auto-increment state.

INSERT INTO companies (slug, name, website, industry, size, founded_year, country, city, location, description, status, verified) VALUES
('pepper-leaf','Pepper Leaf','https://www.pepperleaf.pl/','Restaurant / Kerala Food',NULL,NULL,'Poland','Kraków','Kraków, Poland','Restaurant / Kerala Food company founded by Four IT engineers from Kerala in Kraków, Poland.','approved',0),
('kerala2krakow','Kerala2Krakow (K2K) Restaurant','https://k2krestaurant.com','Restaurant / Kerala Food',NULL,NULL,'Poland','Kraków','Kraków, Poland','Restaurant / Kerala Food company founded by Lijo Philip in Kraków, Poland.','approved',0),
('kalikut-1498','Kalikut 1498',NULL,'Food / Consumer',NULL,NULL,'Poland','Kraków','Kraków, Poland','Food / Consumer company founded by Lijo Philip in Kraków, Poland.','approved',0),
('kerala-cash-and-carry','Kerala Cash and Carry',NULL,'Kerala Grocery / Food Retail / Online Retail',NULL,NULL,'Poland','Kraków','Kraków, Poland','Kerala Grocery / Food Retail / Online Retail company founded by Lijo Philip in Kraków, Poland.','approved',0),
('malayali-beer','MALAYALI','https://malayali.rocks/','Food / Consumer',NULL,NULL,'Poland','Warsaw','Warsaw, Poland','Food / Consumer company founded by Chandramohan Nallur, Sargheve Sukumaran, Pradeep Kumar Nayar, Padma Kumar Chungath Karunakaran in Warsaw, Poland.','approved',0),
('mercado360-ayurvod','Mercado 360 / Ayurvod','https://www.mercado360.eu/','Professional Services',NULL,NULL,'Poland','Warsaw','Warsaw, Poland','Professional Services company founded by Midhun Mohan V.M. in Warsaw, Poland.','approved',0),
('united-kerala-international','United Kerala International Sp. z o.o.',NULL,'Business / Immigration Services',NULL,NULL,'Poland','Kraków','Kraków, Poland','Business / Immigration Services company founded by Wasim Akram Nazeer Reena, Leo Kolady Babu in Kraków, Poland.','pending',0),
('madras-bistro','Madras Bistro','https://madrasbistro.pl/','Restaurant / South Indian / Kerala Food',NULL,NULL,'Poland','Kraków','Kraków, Poland','Restaurant / South Indian / Kerala Food company founded by Livin Victor in Kraków, Poland.','pending',0),
('kera-oriental','KERA Oriental','https://www.keraoriental.pl/','Healthcare / Ayurveda / Wellness',NULL,NULL,'Poland','Kraków','Kraków, Poland','Healthcare / Ayurveda / Wellness company founded by Rejit Jose in Kraków, Poland.','pending',0),
('ayura-ayurveda','Ayura Ayurveda',NULL,'Healthcare / Ayurveda / Wellness',NULL,NULL,'Poland','Kraków','Kraków, Poland','Healthcare / Ayurveda / Wellness company founded by Dr Vijesh M. in Kraków, Poland.','pending',0);

INSERT INTO founders (company_id, name, email, linkedin, show_email) VALUES
((SELECT id FROM companies WHERE slug='pepper-leaf'),'Four IT engineers from Kerala',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kerala2krakow'),'Lijo Philip',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kalikut-1498'),'Lijo Philip',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kerala-cash-and-carry'),'Lijo Philip',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='malayali-beer'),'Chandramohan Nallur',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='malayali-beer'),'Sargheve Sukumaran',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='malayali-beer'),'Pradeep Kumar Nayar',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='malayali-beer'),'Padma Kumar Chungath Karunakaran',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='mercado360-ayurvod'),'Midhun Mohan V.M.',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='united-kerala-international'),'Wasim Akram Nazeer Reena',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='united-kerala-international'),'Leo Kolady Babu',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='madras-bistro'),'Livin Victor',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kera-oriental'),'Rejit Jose',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='ayura-ayurveda'),'Dr Vijesh M.',NULL,NULL,0);
