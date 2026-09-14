-- Kerala Founders: add Romania, Malta, Slovakia, Slovenia companies (batch 7)
-- Safe to run regardless of production's current auto-increment state.

INSERT INTO companies (slug, name, website, industry, size, founded_year, country, city, location, description, status, verified) VALUES
('d2hire-recruitment','D2HIRE Recruitment Solutions S.R.L.','https://d2hire.com/','Business / Immigration Services',NULL,NULL,'Romania','Ploiești','Ploiești, Romania','Business / Immigration Services company founded by Prijoy Mathew Thomas in Ploiești, Romania.','pending',0),
('naan-bar','Naan Bar','https://www.naanbar.com/','Restaurant / Indian / South Indian',NULL,NULL,'Malta','Valletta','Valletta, Malta','Restaurant / Indian / South Indian company founded by Amruda Nair in Valletta, Malta.','approved',0),
('mez-indo-coastal-cuisine','MÉZ Indo-Coastal Cuisine','https://mezrestaurants.com/','Restaurant / South Indian / Kerala Food',NULL,NULL,'Malta','St Julian''s','St Julian''s, Malta','Restaurant / South Indian / Kerala Food company founded by Amruda Nair in St Julian''s, Malta.','approved',0),
('chatterbox-malta','Chatterbox','https://www.araiyahotels.com/','Restaurant / Indian / South Indian',NULL,NULL,'Malta','St Paul''s Bay','St Paul''s Bay, Malta','Restaurant / Indian / South Indian company founded by Amruda Nair in St Paul''s Bay, Malta.','approved',0),
('michael-paul-zachariah','Chef Michael Paul Zachariah',NULL,'Restaurant / Food & Beverage',NULL,NULL,'Malta','Floriana','Floriana, Malta','Restaurant / Food & Beverage company founded by Michael Paul Zachariah in Floriana, Malta.','approved',0),
('kerala-exotic-supermarket','Kerala Exotic Supermarket','https://keralasupermarket.sk/','Kerala Grocery / Food Retail / Online Retail',NULL,NULL,'Slovakia','Bratislava','Bratislava, Slovakia','Kerala Grocery / Food Retail / Online Retail company founded by Avinash Vijayakumar, Jose Nilavoor in Bratislava, Slovakia.','approved',0),
('avn-kerala','AVN Kerala s.r.o.',NULL,'Professional Services',NULL,NULL,'Slovakia','Bratislava','Bratislava, Slovakia','Professional Services company founded by Avinash Vijayakumar in Bratislava, Slovakia.','pending',0),
('ayaan-sro','AYAAN s.r.o.',NULL,'Professional Services',NULL,NULL,'Slovakia','Bratislava','Bratislava, Slovakia','Professional Services company founded by Avinash Vijayakumar in Bratislava, Slovakia.','pending',0),
('euro-middle-east','Euro Middle East s.r.o.',NULL,'Professional Services',NULL,NULL,'Slovakia','Bratislava','Bratislava, Slovakia','Professional Services company founded by Jamsheer Pengattiri in Bratislava, Slovakia.','approved',0),
('euroxis','EUROXIS s.r.o.',NULL,'Professional Services',NULL,NULL,'Slovakia','Bratislava','Bratislava, Slovakia','Professional Services company founded by Jamsheer Pengattiri, Sagarkhan Kassim in Bratislava, Slovakia.','approved',0),
('euromaxis-pengattiri','Jamsheer Pengattiri - EUROMAXIS',NULL,'Retail / Consumer',NULL,NULL,'Slovakia','Gabčíkovo','Gabčíkovo, Slovakia','Retail / Consumer company founded by Jamsheer Pengattiri in Gabčíkovo, Slovakia.','approved',0),
('food-pack','FOOD PACK d.o.o.',NULL,'Restaurant / Food & Beverage',NULL,NULL,'Slovenia','Ljubljana','Ljubljana, Slovenia','Restaurant / Food & Beverage company founded by Julija Puthuveettil in Ljubljana, Slovenia.','pending',0),
('bollywood-ljubljana','Bollywood','https://www.bollywood.si','Restaurant / Indian / South Indian',NULL,NULL,'Slovenia','Ljubljana','Ljubljana, Slovenia','Restaurant / Indian / South Indian company founded by Ljubomir Petrović, Puthuveettil Nishabudheen in Ljubljana, Slovenia.','pending',0),
('ila-and-co','ILA & Co.',NULL,'Startup Advisory / Innovation',NULL,NULL,'Slovenia','Ljubljana','Ljubljana, Slovenia','Startup Advisory / Innovation company founded by Akhil Mathew in Ljubljana, Slovenia.','pending',0),
('maya-ayurveda','Maya Ayurveda','https://www.mayaayurveda.com/','Healthcare / Ayurveda / Wellness',NULL,NULL,'Slovenia','Ljubljana','Ljubljana, Slovenia','Healthcare / Ayurveda / Wellness company founded by Dr. Siju Sukumaran in Ljubljana, Slovenia.','approved',0),
('ega-vida-wellness','EGA VIDA WELLNESS','https://www.egavida.eu','Healthcare / Ayurveda / Wellness',NULL,NULL,'Slovenia','Domanjševci','Domanjševci, Slovenia','Healthcare / Ayurveda / Wellness company founded by Shiyas Hussain Sheriff Hussain in Domanjševci, Slovenia.','approved',0);

INSERT INTO founders (company_id, name, email, linkedin, show_email) VALUES
((SELECT id FROM companies WHERE slug='d2hire-recruitment'),'Prijoy Mathew Thomas',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='naan-bar'),'Amruda Nair',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='mez-indo-coastal-cuisine'),'Amruda Nair',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='chatterbox-malta'),'Amruda Nair',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='michael-paul-zachariah'),'Michael Paul Zachariah',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kerala-exotic-supermarket'),'Avinash Vijayakumar',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kerala-exotic-supermarket'),'Jose Nilavoor',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='avn-kerala'),'Avinash Vijayakumar',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='ayaan-sro'),'Avinash Vijayakumar',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='euro-middle-east'),'Jamsheer Pengattiri',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='euroxis'),'Jamsheer Pengattiri',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='euroxis'),'Sagarkhan Kassim',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='euromaxis-pengattiri'),'Jamsheer Pengattiri',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='food-pack'),'Julija Puthuveettil',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='bollywood-ljubljana'),'Ljubomir Petrović',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='bollywood-ljubljana'),'Puthuveettil Nishabudheen',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='ila-and-co'),'Akhil Mathew',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='maya-ayurveda'),'Dr. Siju Sukumaran',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='ega-vida-wellness'),'Shiyas Hussain Sheriff Hussain',NULL,NULL,0);
