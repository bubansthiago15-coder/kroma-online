ALTER TABLE pedidos
  ADD COLUMN ciudad_destinatario_historica VARCHAR(100) NULL AFTER dni_destinatario_historico,
  ADD COLUMN codigo_postal_destinatario_historico VARCHAR(10) NULL AFTER ciudad_destinatario_historica;
