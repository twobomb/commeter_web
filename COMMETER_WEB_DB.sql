-- Valentina Studio --
-- MySQL dump --
-- ---------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- ---------------------------------------------------------


-- CREATE TABLE "category" -------------------------------------
CREATE TABLE `category`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`is_can_add` TinyInt( 1 ) NOT NULL DEFAULT 1,
	`name` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`is_hidden` TinyInt( 1 ) NOT NULL DEFAULT 0,
	`is_deleted` TinyInt( 1 ) NOT NULL DEFAULT 0,
	`sort_id` Int( 11 ) NOT NULL DEFAULT 1,
	`parent_id` Int( 11 ) NULL DEFAULT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 110;
-- -------------------------------------------------------------


-- CREATE TABLE "department" -----------------------------------
CREATE TABLE `department`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`name` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`sort_id` Int( 11 ) NOT NULL,
	`parent_id` Int( 11 ) NULL DEFAULT NULL,
	`is_deleted` TinyInt( 1 ) NOT NULL DEFAULT 0,
	`is_department` TinyInt( 255 ) NOT NULL DEFAULT 0,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 206;
-- -------------------------------------------------------------


-- CREATE TABLE "dictinary" ------------------------------------
CREATE TABLE `dictinary`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`name` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 85;
-- -------------------------------------------------------------


-- CREATE TABLE "dictinary_item" -------------------------------
CREATE TABLE `dictinary_item`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`value` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`dictinary_id` Int( 11 ) NOT NULL,
	`sort_id` Int( 255 ) NOT NULL DEFAULT 1,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 495;
-- -------------------------------------------------------------


-- CREATE TABLE "employee" -------------------------------------
CREATE TABLE `employee`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`first_name` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`second_name` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`last_name` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`post` VarChar( 512 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`cabinet` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`is_deleted` TinyInt( 1 ) NOT NULL DEFAULT 0,
	`sort_id` Int( 11 ) NOT NULL DEFAULT 1,
	`department_id` Int( 11 ) NOT NULL,
	`is_responsible` TinyInt( 255 ) NOT NULL DEFAULT 0,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 825;
-- -------------------------------------------------------------


-- CREATE TABLE "expression" -----------------------------------
CREATE TABLE `expression`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`type` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`parent_id` Int( 11 ) NULL DEFAULT NULL,
	`request_id` Int( 11 ) NOT NULL,
	`field_id` Int( 11 ) NULL DEFAULT NULL,
	`sort_id` Int( 11 ) NOT NULL,
	`condition` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`condition_type` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`negative` TinyInt( 1 ) NOT NULL,
	`isGroup` TinyInt( 1 ) NOT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 1;
-- -------------------------------------------------------------


-- CREATE TABLE "expression_values" ----------------------------
CREATE TABLE `expression_values`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`text` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`expression_id` Int( 11 ) NOT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 1;
-- -------------------------------------------------------------


-- CREATE TABLE "feature" --------------------------------------
CREATE TABLE `feature`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`is_required` TinyInt( 1 ) NOT NULL DEFAULT 0,
	`dictinary_id` Int( 11 ) NULL DEFAULT NULL,
	`sort_id` Int( 11 ) NOT NULL DEFAULT 1,
	`name` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`type` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 105;
-- -------------------------------------------------------------


-- CREATE TABLE "feature_values" -------------------------------
CREATE TABLE `feature_values`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`value` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`feature_id` Int( 11 ) NOT NULL,
	`item_id` VarChar( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 62679;
-- -------------------------------------------------------------


-- CREATE TABLE "featurecategories" ----------------------------
CREATE TABLE `featurecategories`( 
	`Feature_id` Int( 11 ) NOT NULL,
	`Category_id` Int( 11 ) NOT NULL,
	PRIMARY KEY ( `Feature_id`, `Category_id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB;
-- -------------------------------------------------------------


-- CREATE TABLE "field" ----------------------------------------
CREATE TABLE `field`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`name` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`feature_id` Int( 11 ) NULL DEFAULT NULL,
	`column_excel_width` Int( 11 ) NOT NULL,
	`column_valign` Int( 11 ) NOT NULL,
	`column_halign` Int( 11 ) NOT NULL,
	`type` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 1;
-- -------------------------------------------------------------


-- CREATE TABLE "field_views" ----------------------------------
CREATE TABLE `field_views`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`request_id` Int( 11 ) NOT NULL,
	`field_id` Int( 11 ) NOT NULL,
	`sort_id` Int( 11 ) NOT NULL,
	`value` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 1;
-- -------------------------------------------------------------


-- CREATE TABLE "history" --------------------------------------
CREATE TABLE `history`( 
	`name` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`id` Int( 255 ) NOT NULL,
	`data` Text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`inv_num` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`date` DateTime NOT NULL,
	`action` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`department` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`category` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB;
-- -------------------------------------------------------------


-- CREATE TABLE "item" -----------------------------------------
CREATE TABLE `item`( 
	`id` Int( 128 ) AUTO_INCREMENT NOT NULL,
	`employee_id` Int( 11 ) NULL DEFAULT NULL,
	`category_id` Int( 11 ) NOT NULL,
	`inv_num` VarChar( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`workspace` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`name` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`date_change` DateTime NULL DEFAULT NULL,
	`is_written_off` TinyInt( 1 ) NOT NULL DEFAULT 0,
	`department_id` Int( 11 ) NOT NULL,
	`responsible_employee_id` Int( 255 ) NOT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 7671;
-- -------------------------------------------------------------


-- CREATE TABLE "repairs" --------------------------------------
CREATE TABLE `repairs`( 
	`id` Int( 128 ) AUTO_INCREMENT NOT NULL,
	`date_change` DateTime NULL DEFAULT NULL,
	`item_id` Int( 128 ) NOT NULL,
	`sum` Double( 22, 0 ) NULL DEFAULT NULL,
	`date` Date NOT NULL,
	`description` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`type` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 2;
-- -------------------------------------------------------------


-- CREATE TABLE "requests" -------------------------------------
CREATE TABLE `requests`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`name` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`order_by_field_id` Int( 11 ) NOT NULL,
	`sort_id` Int( 11 ) NOT NULL,
	`user_id` Int( 11 ) NOT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 1;
-- -------------------------------------------------------------


-- CREATE TABLE "rules" ----------------------------------------
CREATE TABLE `rules`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`user_id` Int( 11 ) NOT NULL,
	`permission` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`value` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 82625;
-- -------------------------------------------------------------


-- CREATE TABLE "tags" -----------------------------------------
CREATE TABLE `tags`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`name` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`user_id` Int( 255 ) NOT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 19;
-- -------------------------------------------------------------


-- CREATE TABLE "tags_items" -----------------------------------
CREATE TABLE `tags_items`( 
	`tag_id` Int( 11 ) NOT NULL,
	`Item_id` Int( 128 ) NOT NULL,
	PRIMARY KEY ( `tag_id`, `Item_id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB;
-- -------------------------------------------------------------


-- CREATE TABLE "transfer" -------------------------------------
CREATE TABLE `transfer`( 
	`id` Int( 128 ) AUTO_INCREMENT NOT NULL,
	`date` Date NULL DEFAULT NULL,
	`description` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`workplace_from` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`workplace_to` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`department_id_from` Int( 11 ) NOT NULL,
	`department_id_to` Int( 11 ) NOT NULL,
	`date_change` DateTime NOT NULL,
	`type` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`responsible_employee_id_to` Int( 255 ) NULL DEFAULT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 646;
-- -------------------------------------------------------------


-- CREATE TABLE "transfer_items" -------------------------------
CREATE TABLE `transfer_items`( 
	`transfer_id` Int( 255 ) NOT NULL,
	`item_id` Int( 255 ) NOT NULL )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB;
-- -------------------------------------------------------------


-- CREATE TABLE "users" ----------------------------------------
CREATE TABLE `users`( 
	`id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`name` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`login` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`pwd_hash` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`role` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'user',
	`block` TinyInt( 255 ) NOT NULL DEFAULT 0,
	`authKey` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`accessToken` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`last_activity` DateTime NULL DEFAULT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 1499;
-- -------------------------------------------------------------


-- CREATE TABLE "written_off" ----------------------------------
CREATE TABLE `written_off`( 
	`id` VarChar( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`date_change` DateTime NULL DEFAULT NULL,
	`is_sync` TinyInt( 1 ) NOT NULL,
	`item_id` Int( 128 ) NULL DEFAULT NULL,
	`date` DateTime NOT NULL,
	`date_order` DateTime NULL DEFAULT NULL,
	`description` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`order_number` LongText CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	PRIMARY KEY ( `id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB;
-- -------------------------------------------------------------


-- CREATE TABLE "advanced_departments" -------------------------
CREATE TABLE `advanced_departments`( 
	`department_id` Int( 255 ) NOT NULL,
	`employee_id` Int( 255 ) NOT NULL )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB;
-- -------------------------------------------------------------


-- CREATE INDEX "IX2_parent_id" --------------------------------
CREATE INDEX `IX2_parent_id` USING BTREE ON `category`( `parent_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX3_parent_id" --------------------------------
CREATE INDEX `IX3_parent_id` USING BTREE ON `department`( `parent_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "Index_1" --------------------------------------
CREATE INDEX `Index_1` USING BTREE ON `dictinary`( `name` );
-- -------------------------------------------------------------


-- CREATE INDEX "index IX1_dictinary_id" -----------------------
CREATE INDEX `index IX1_dictinary_id` USING BTREE ON `dictinary_item`( `dictinary_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX2_department_id" ----------------------------
CREATE INDEX `IX2_department_id` USING BTREE ON `employee`( `department_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX1_request_id" -------------------------------
CREATE INDEX `IX1_request_id` USING BTREE ON `expression`( `request_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX_field_id" ----------------------------------
CREATE INDEX `IX_field_id` USING BTREE ON `expression`( `field_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX_expression_id" -----------------------------
CREATE INDEX `IX_expression_id` USING BTREE ON `expression_values`( `expression_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX_dictinary_id" ------------------------------
CREATE INDEX `IX_dictinary_id` USING BTREE ON `feature`( `dictinary_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX1_feature_id" -------------------------------
CREATE INDEX `IX1_feature_id` USING BTREE ON `feature_values`( `feature_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX_Item_id6" ----------------------------------
CREATE INDEX `IX_Item_id6` USING BTREE ON `feature_values`( `item_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX_Category_id" -------------------------------
CREATE INDEX `IX_Category_id` USING BTREE ON `featurecategories`( `Category_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX_Feature_id" --------------------------------
CREATE INDEX `IX_Feature_id` USING BTREE ON `featurecategories`( `Feature_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX2_feature_id" -------------------------------
CREATE INDEX `IX2_feature_id` USING BTREE ON `field`( `feature_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX1_field_id" ---------------------------------
CREATE INDEX `IX1_field_id` USING BTREE ON `field_views`( `field_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX2_request_id" -------------------------------
CREATE INDEX `IX2_request_id` USING BTREE ON `field_views`( `request_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "Index_1" --------------------------------------
CREATE INDEX `Index_1` USING BTREE ON `history`( `id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX1_category_id" ------------------------------
CREATE INDEX `IX1_category_id` USING BTREE ON `item`( `category_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX_department_id" -----------------------------
CREATE INDEX `IX_department_id` USING BTREE ON `item`( `department_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX_employee_id" -------------------------------
CREATE INDEX `IX_employee_id` USING BTREE ON `item`( `employee_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX2_item_id" ----------------------------------
CREATE INDEX `IX2_item_id` USING BTREE ON `repairs`( `item_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX1_user_id" ----------------------------------
CREATE INDEX `IX1_user_id` USING BTREE ON `requests`( `user_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX_order_by_field_id" -------------------------
CREATE INDEX `IX_order_by_field_id` USING BTREE ON `requests`( `order_by_field_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX2_user_id" ----------------------------------
CREATE INDEX `IX2_user_id` USING BTREE ON `rules`( `user_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "lnk_users_tags" -------------------------------
CREATE INDEX `lnk_users_tags` USING BTREE ON `tags`( `user_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX_Group_id" ----------------------------------
CREATE INDEX `IX_Group_id` USING BTREE ON `tags_items`( `tag_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX_Item_id" -----------------------------------
CREATE INDEX `IX_Item_id` USING BTREE ON `tags_items`( `Item_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX_department_id_from" ------------------------
CREATE INDEX `IX_department_id_from` USING BTREE ON `transfer`( `department_id_from` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX_department_id_to" --------------------------
CREATE INDEX `IX_department_id_to` USING BTREE ON `transfer`( `department_id_to` );
-- -------------------------------------------------------------


-- CREATE INDEX "lnk_item_transfer_items" ----------------------
CREATE INDEX `lnk_item_transfer_items` USING BTREE ON `transfer_items`( `item_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "lnk_transfer_transfer_items" ------------------
CREATE INDEX `lnk_transfer_transfer_items` USING BTREE ON `transfer_items`( `transfer_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "IX4_item_id" ----------------------------------
CREATE INDEX `IX4_item_id` USING BTREE ON `written_off`( `item_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "lnk_department_advanced_departments" ----------
CREATE INDEX `lnk_department_advanced_departments` USING BTREE ON `advanced_departments`( `department_id` );
-- -------------------------------------------------------------


-- CREATE INDEX "lnk_employee_advanced_departments" ------------
CREATE INDEX `lnk_employee_advanced_departments` USING BTREE ON `advanced_departments`( `employee_id` );
-- -------------------------------------------------------------


-- CREATE LINK "lnk_category_category" -------------------------
ALTER TABLE `category`
	ADD CONSTRAINT `lnk_category_category` FOREIGN KEY ( `parent_id` )
	REFERENCES `category`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_department_department" ---------------------
ALTER TABLE `department`
	ADD CONSTRAINT `lnk_department_department` FOREIGN KEY ( `parent_id` )
	REFERENCES `department`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_dictinary_dictinary_item" ------------------
ALTER TABLE `dictinary_item`
	ADD CONSTRAINT `lnk_dictinary_dictinary_item` FOREIGN KEY ( `dictinary_id` )
	REFERENCES `dictinary`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_department_employee" -----------------------
ALTER TABLE `employee`
	ADD CONSTRAINT `lnk_department_employee` FOREIGN KEY ( `department_id` )
	REFERENCES `department`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "FK_expression_field_field_id" ------------------
ALTER TABLE `expression`
	ADD CONSTRAINT `FK_expression_field_field_id` FOREIGN KEY ( `field_id` )
	REFERENCES `field`( `id` )
	ON DELETE No Action
	ON UPDATE No Action;
-- -------------------------------------------------------------


-- CREATE LINK "FK_expression_requests_request_id" -------------
ALTER TABLE `expression`
	ADD CONSTRAINT `FK_expression_requests_request_id` FOREIGN KEY ( `request_id` )
	REFERENCES `requests`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "FK_expression_values_expression_expression_id" -
ALTER TABLE `expression_values`
	ADD CONSTRAINT `FK_expression_values_expression_expression_id` FOREIGN KEY ( `expression_id` )
	REFERENCES `expression`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_feature_feature_values" --------------------
ALTER TABLE `feature_values`
	ADD CONSTRAINT `lnk_feature_feature_values` FOREIGN KEY ( `feature_id` )
	REFERENCES `feature`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_category_featurecategories" ----------------
ALTER TABLE `featurecategories`
	ADD CONSTRAINT `lnk_category_featurecategories` FOREIGN KEY ( `Category_id` )
	REFERENCES `category`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_feature_featurecategories" -----------------
ALTER TABLE `featurecategories`
	ADD CONSTRAINT `lnk_feature_featurecategories` FOREIGN KEY ( `Feature_id` )
	REFERENCES `feature`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_feature_field" -----------------------------
ALTER TABLE `field`
	ADD CONSTRAINT `lnk_feature_field` FOREIGN KEY ( `feature_id` )
	REFERENCES `feature`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "FK_field_views_field_field_id" -----------------
ALTER TABLE `field_views`
	ADD CONSTRAINT `FK_field_views_field_field_id` FOREIGN KEY ( `field_id` )
	REFERENCES `field`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "FK_field_views_requests_request_id" ------------
ALTER TABLE `field_views`
	ADD CONSTRAINT `FK_field_views_requests_request_id` FOREIGN KEY ( `request_id` )
	REFERENCES `requests`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_category_item" -----------------------------
ALTER TABLE `item`
	ADD CONSTRAINT `lnk_category_item` FOREIGN KEY ( `category_id` )
	REFERENCES `category`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_item_repairs" ------------------------------
ALTER TABLE `repairs`
	ADD CONSTRAINT `lnk_item_repairs` FOREIGN KEY ( `item_id` )
	REFERENCES `item`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "FK_requests_field_order_by_field_id" -----------
ALTER TABLE `requests`
	ADD CONSTRAINT `FK_requests_field_order_by_field_id` FOREIGN KEY ( `order_by_field_id` )
	REFERENCES `field`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_users_requests" ----------------------------
ALTER TABLE `requests`
	ADD CONSTRAINT `lnk_users_requests` FOREIGN KEY ( `user_id` )
	REFERENCES `users`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_users_rules" -------------------------------
ALTER TABLE `rules`
	ADD CONSTRAINT `lnk_users_rules` FOREIGN KEY ( `user_id` )
	REFERENCES `users`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_users_tags" --------------------------------
ALTER TABLE `tags`
	ADD CONSTRAINT `lnk_users_tags` FOREIGN KEY ( `user_id` )
	REFERENCES `users`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "FK_GroupItems_groups_Group_id" -----------------
ALTER TABLE `tags_items`
	ADD CONSTRAINT `FK_GroupItems_groups_Group_id` FOREIGN KEY ( `tag_id` )
	REFERENCES `tags`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_item_tags_items" ---------------------------
ALTER TABLE `tags_items`
	ADD CONSTRAINT `lnk_item_tags_items` FOREIGN KEY ( `Item_id` )
	REFERENCES `item`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_department_transfer" -----------------------
ALTER TABLE `transfer`
	ADD CONSTRAINT `lnk_department_transfer` FOREIGN KEY ( `department_id_from` )
	REFERENCES `department`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_department_transfer_2" ---------------------
ALTER TABLE `transfer`
	ADD CONSTRAINT `lnk_department_transfer_2` FOREIGN KEY ( `department_id_to` )
	REFERENCES `department`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_item_transfer_items" -----------------------
ALTER TABLE `transfer_items`
	ADD CONSTRAINT `lnk_item_transfer_items` FOREIGN KEY ( `item_id` )
	REFERENCES `item`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_transfer_transfer_items" -------------------
ALTER TABLE `transfer_items`
	ADD CONSTRAINT `lnk_transfer_transfer_items` FOREIGN KEY ( `transfer_id` )
	REFERENCES `transfer`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_item_written_off" --------------------------
ALTER TABLE `written_off`
	ADD CONSTRAINT `lnk_item_written_off` FOREIGN KEY ( `item_id` )
	REFERENCES `item`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_department_advanced_departments" -----------
ALTER TABLE `advanced_departments`
	ADD CONSTRAINT `lnk_department_advanced_departments` FOREIGN KEY ( `department_id` )
	REFERENCES `department`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


-- CREATE LINK "lnk_employee_advanced_departments" -------------
ALTER TABLE `advanced_departments`
	ADD CONSTRAINT `lnk_employee_advanced_departments` FOREIGN KEY ( `employee_id` )
	REFERENCES `employee`( `id` )
	ON DELETE Cascade
	ON UPDATE Cascade;
-- -------------------------------------------------------------


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- ---------------------------------------------------------


