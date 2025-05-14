-- Structure de la table `user`
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertion d'un utilisateur admin par défaut (mot de passe: admin123)
INSERT INTO `user` (`nom`, `prenom`, `email`, `password`, `role`) VALUES
('Admin', 'System', 'admin@sharemyride.com', '$2y$10$J9xOgmRnNS.ZR/WaEcZ9HOyA7GEfNYxYv3yK45.2vXitlG5cGR7US', 'admin'); 