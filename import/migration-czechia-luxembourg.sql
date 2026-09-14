-- Kerala Founders: add Czechia, Luxembourg companies (batch 5)
-- Safe to run regardless of production's current auto-increment state:
-- companies are inserted without explicit ids, and founders are linked
-- back to their company by slug (unique) rather than a hardcoded id.

INSERT INTO companies (slug, name, website, industry, size, founded_year, country, city, location, description, status, verified) VALUES
('ayush-consultants','Ayush Consultants s.r.o.',NULL,'Professional Services',NULL,NULL,'Czechia','Prague','Prague, Czechia','Professional Services company founded by Ragesh Kumar Kangaparambil Gopalan in Prague, Czechia.','approved',0),
('jpn-international','JPN International s.r.o.',NULL,'Professional Services',NULL,NULL,'Czechia','Prague','Prague, Czechia','Professional Services company founded by Jamespaul Jeejo Nooranal, Jeejo Paulose, Bindu Jeejo in Prague, Czechia.','approved',0),
('sandeep-soman','Sandeep Soman',NULL,'Professional Services',NULL,NULL,'Czechia','Prague','Prague, Czechia','Professional Services company founded by Sandeep Soman in Prague, Czechia.','approved',0),
('seal-tech','Seal Tech s.r.o.',NULL,'Professional Services',NULL,NULL,'Czechia','Brno','Brno, Czechia','Professional Services company founded by Fatima Gasna Cheryakkatt in Brno, Czechia.','approved',0),
('yummy-bites-by-jithu','Yummy Bites by Jithu','https://www.yummybites.lu/','Food / Consumer',NULL,NULL,'Luxembourg','Belvaux','Belvaux, Luxembourg','Food / Consumer company founded by Jithu Davis in Belvaux, Luxembourg.','approved',0);

INSERT INTO founders (company_id, name, email, linkedin, show_email) VALUES
((SELECT id FROM companies WHERE slug='ayush-consultants'),'Ragesh Kumar Kangaparambil Gopalan',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='jpn-international'),'Jamespaul Jeejo Nooranal',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='jpn-international'),'Jeejo Paulose',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='jpn-international'),'Bindu Jeejo',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='sandeep-soman'),'Sandeep Soman',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='seal-tech'),'Fatima Gasna Cheryakkatt',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='yummy-bites-by-jithu'),'Jithu Davis',NULL,NULL,0);
