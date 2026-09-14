-- Kerala Founders: add Cyprus, Greece companies (batch 6)
-- Safe to run regardless of production's current auto-increment state.

INSERT INTO companies (slug, name, website, industry, size, founded_year, country, city, location, description, status, verified) VALUES
('best-exotic-kerala-kitchen','The Best Exotic Kerala Kitchen',NULL,'Catering / Kerala Food',NULL,NULL,'Cyprus','Nicosia','Nicosia, Cyprus','Catering / Kerala Food company founded by Serene Mary Tharian in Nicosia, Cyprus.','approved',0),
('kalikut-story','The Kalikut Story',NULL,'Retail / Consumer',NULL,NULL,'Cyprus','Nicosia','Nicosia, Cyprus','Retail / Consumer company founded by Serene Mary Tharian, Gabriella Paphitis in Nicosia, Cyprus.','pending',0),
('golden-ratio-limited','Golden Ratio Limited',NULL,'Professional Services',NULL,NULL,'Cyprus','Nicosia','Nicosia, Cyprus','Professional Services company founded by Abraham Malayatoor, Mathews Thomas Malayatoor in Nicosia, Cyprus.','pending',0),
('instamat-cyprus','Instamat Cyprus Limited',NULL,'Professional Services',NULL,NULL,'Cyprus','Nicosia','Nicosia, Cyprus','Professional Services company founded by Abraham Malayatoor, Boris Zenkov in Nicosia, Cyprus.','pending',0),
('athens-insider','Insider Publications / Athens Insider','https://www.athensinsider.com/','Media / Creative',NULL,NULL,'Greece','Athens','Athens, Greece','Media / Creative company founded by Sudha Nair-Iliades in Athens, Greece.','pending',0);

INSERT INTO founders (company_id, name, email, linkedin, show_email) VALUES
((SELECT id FROM companies WHERE slug='best-exotic-kerala-kitchen'),'Serene Mary Tharian',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kalikut-story'),'Serene Mary Tharian',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='kalikut-story'),'Gabriella Paphitis',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='golden-ratio-limited'),'Abraham Malayatoor',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='golden-ratio-limited'),'Mathews Thomas Malayatoor',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='instamat-cyprus'),'Abraham Malayatoor',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='instamat-cyprus'),'Boris Zenkov',NULL,NULL,0),
((SELECT id FROM companies WHERE slug='athens-insider'),'Sudha Nair-Iliades',NULL,NULL,0);
