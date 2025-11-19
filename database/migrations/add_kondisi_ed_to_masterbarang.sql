-- Migration: Add kondisi and ed fields to masterbarang table
-- Date: 2024-11-19
-- Description: Add kondisi (varchar 100) and ed (varchar 50) fields after nie field

ALTER TABLE masterbarang 
ADD COLUMN kondisi VARCHAR(100) NULL AFTER nie,
ADD COLUMN ed VARCHAR(50) NULL AFTER kondisi;

