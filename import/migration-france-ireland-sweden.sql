-- Kerala Founders: add France, Ireland, Sweden companies (batch 3)
-- Safe to run regardless of production's current auto-increment state:
-- companies are inserted without explicit ids, and founders are linked
-- back to their company by slug (unique) rather than a hardcoded id.

INSERT INTO companies (slug, name, website, industry, size, founded_year, country, city, location, description, status, verified) VALUES
('kallu-and-co','Kallu & Co',NULL,'Restaurant / Kerala Food',NULL,NULL,'France','Paris','Paris, France','Restaurant / Kerala Food company founded by Alex Jerry, Grishma Satpathy, Ashik Roy in Paris, France.','approved',0),
('mama-spice','Mama Spice','https://mama-spice.com/','Restaurant / Kerala Food',NULL,NULL,'France','Marseille','Marseille, France','Restaurant / Kerala Food company founded by Devaky Sivadasan in Marseille, France.','approved',0),
('girelle-production','Girelle Production',NULL,'Media / Creative',NULL,NULL,'France','','France','Media / Creative company founded by Vinnie Ann Bose in France.','approved',0),
('3-leaves','3 Leaves','https://3leaves.ie/','Restaurant / Indian / South Indian',NULL,NULL,'Ireland','Dublin','Dublin, Ireland','Restaurant / Indian / South Indian company founded by Santhosh Thomas, Milli Mathew in Dublin, Ireland.','approved',0),
('kasi-cafe','Kasi Café','https://kasikitchen.com/','Restaurant / South Indian / Kerala Food',NULL,NULL,'Ireland','Dublin','Dublin, Ireland','Restaurant / South Indian / Kerala Food company founded by Daril Chamakkalayil Thomas, Dipin Chamakkalayil Thomas in Dublin, Ireland.','approved',0),
('indian-spice-kitchen','Indian Spice Kitchen','https://indianspice-kitchen.ie/','Restaurant / South Indian / Kerala Food',NULL,NULL,'Ireland','Cork','Cork, Ireland','Restaurant / South Indian / Kerala Food company founded by Jessily Panicker, Dinesh John in Cork, Ireland.','approved',0),
('olivez','Olivez','https://www.olivez.ie/','Restaurant / South Indian / Kerala Food',NULL,NULL,'Ireland','Dublin','Dublin, Ireland','Restaurant / South Indian / Kerala Food company founded by Abraham Mathew in Dublin, Ireland.','pending',0),
('pulari-restaurant','Pulari Restaurant','https://www.pulari.ie/','Restaurant / South Indian / Kerala Food',NULL,NULL,'Ireland','Dublin','Dublin, Ireland','Restaurant / South Indian / Kerala Food company founded by Bijukuttan in Dublin, Ireland.','pending',0),
('kerala-royal-caterers','Kerala Royal Caterers','https://www.royalcatersdublin.com/','Catering / Kerala Food',NULL,NULL,'Ireland','Ashbourne','Ashbourne, Ireland','Catering / Kerala Food company founded by Abhilash in Ashbourne, Ireland.','pending',0),
('smaqo-foods','SMAQO / Smaqo Foods AB','https://smaqo.com/','Food Technology',NULL,NULL,'Sweden','Gothenburg','Gothenburg, Sweden','Food Technology company founded by Ramkumar Balachandran Nair in Gothenburg, Sweden.','approved',0),
('search-indie','Search Indie','https://www.searchindie.com/','AI / Software / Digital',NULL,NULL,'Sweden','Stockholm','Stockholm, Sweden','AI / Software / Digital company founded by Renjith Ramachandran in Stockholm, Sweden.','approved',0),
('kerala-kitchen-nykoping','Kerala Kitchen',NULL,'Catering / Home Food / Kerala Snacks',NULL,NULL,'Sweden','Nyköping','Nyköping, Sweden','Catering / Home Food / Kerala Snacks company founded by Sreekand Bose Vattappallil in Nyköping, Sweden.','pending',0);

INSERT INTO founders (company_id, name, email, linkedin, show_email) VALUES
((SELECT id FROM companies WHERE slug='kallu-and-co'),'Alex Jerry',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kallu-and-co'),'Grishma Satpathy',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kallu-and-co'),'Ashik Roy',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='mama-spice'),'Devaky Sivadasan',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='girelle-production'),'Vinnie Ann Bose',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='3-leaves'),'Santhosh Thomas',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='3-leaves'),'Milli Mathew',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kasi-cafe'),'Daril Chamakkalayil Thomas',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kasi-cafe'),'Dipin Chamakkalayil Thomas',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='indian-spice-kitchen'),'Jessily Panicker',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='indian-spice-kitchen'),'Dinesh John',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='olivez'),'Abraham Mathew',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='pulari-restaurant'),'Bijukuttan',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kerala-royal-caterers'),'Abhilash',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='smaqo-foods'),'Ramkumar Balachandran Nair',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='search-indie'),'Renjith Ramachandran',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kerala-kitchen-nykoping'),'Sreekand Bose Vattappallil',NULL,NULL,0);
