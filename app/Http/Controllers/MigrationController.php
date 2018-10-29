<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class MigrationController extends Controller
{
    public function index()
    {

        // Start transaction!
        DB::connection('mysql2')->beginTransaction();

        try {
            
            //select cases with limit
            $cases = $this->GetAllCases(8,2);
            //counter for inserted_cases
            $number_of_inserted_cases = 0;
            foreach ($cases as $key => $case) {
                $case_id = $case->case_id;
                //select case master data
                $case_data = $this->SelectCase($case_id);
                $case_data["illness_type"] = json_encode([$case_data["illness_type"]]);
                $case_data["typer_id"] = 0;
                $case_partners_data = $this->SelectCasePartners($case_id);
                $case_partners_data["illness_type"] = json_encode([$case_partners_data["illness_type"]]);
                $case_income_data = $this->SelectCaseIncome($case_id);
                $case_support_data = $this->SelectCaseSupport($case_id);
                $case_loan_data = $this->SelectCaseLoan($case_id);
                $case_rooms_data = $this->SelectCaseRooms($case_id);
                $case_childs_data = $this->SelectCaseChilds($case_id);
                $case_roommates_data = $this->SelectCaseRoommates($case_id);
                foreach ($case_childs_data as $key => $value) {
                    $case_childs_data[$key]['illness_type'] = json_encode([$case_childs_data[$key]["illness_type"]]); 
                }

                foreach ($case_roommates_data as $key => $value) {
                    $case_roommates_data[$key]["illness_type"] = json_encode([$case_roommates_data[$key]["illness_type"]]);
                }

                $case_status_data = $this->SelectCaseStatus($case_id);
                // var_dump($case_status_data);die;
                //start transaction
                // DB::connection('mysql2')->transaction(function () use($case_data,$case_income_data,$case_support_data,$case_loan_data,$case_rooms_data,$case_childs_data,$case_roommates_data,$case_partners_data,$case_status_data,&$number_of_inserted_cases) {
                    DB::connection('mysql2')->table('cases')->insert($case_data);
                    DB::connection('mysql2')->table("case_partners")->insert($case_partners_data);                         
                    DB::connection('mysql2')->table("case_income")->insert($case_income_data); 
                    DB::connection('mysql2')->table("case_support")->insert($case_support_data);   
                    DB::connection('mysql2')->table("case_debts")->insert($case_loan_data);
                    DB::connection('mysql2')->table("case_rooms")->insert($case_rooms_data);         
                    DB::connection('mysql2')->table("case_children")->insert($case_childs_data);         
                    DB::connection('mysql2')->table("case_roommates")->insert($case_roommates_data);
                    DB::connection('mysql2')->table("case_statuses")->insert($case_status_data);
                    $number_of_inserted_cases++;
                // });
            }

            // If we reach here, then
            // data is valid and working.
            // Commit the queries!
            DB::connection('mysql2')->commit();

        } catch(\Exception $e){
            DB::connection('mysql2')->rollback();
            throw $e;
        }

        
        
        //print the inserted cases
        echo "<h1>Number OF Inserted Cases $number_of_inserted_cases</h1>";
    }



    private function SelectCasePartners($case_id)
    {
            $data = DB::connection('mysql')->select("SELECT 
            '".$case_id."' `case_id`,
            field_data_field_related_name.`field_related_name_value` `name`,
            field_data_field_mother_gender.`field_mother_gender_value` `gender`,
            field_data_field_mother_age.`field_mother_age_value` `age`,
            field_data_field_mother_id_num.`field_mother_id_num_value` `national_id`,
            field_data_field_mother_marital_status.`field_mother_marital_status_value` `relationship_status`,
            field_data_field_mother_educ.`field_mother_educ_value` `education_status`,
            field_data_field_mother_job.`field_mother_job_value` `work_status`,
            field_data_field_mother_job_desc.`field_mother_job_desc_value` `profession`,
            field_data_field_mother_id_pic.`field_mother_id_pic_fid` `national_id_front`,
            field_data_field_mother_id_pic_back.`field_mother_id_pic_back_fid` `national_id_back`,
            field_data_field_mother_phone.`field_mother_phone_value` `phone`,
            field_data_field_mother_has_disease.`field_mother_has_disease_value` `is_ill`,
            field_data_field_mother_disease_type.`field_mother_disease_type_value` `illness_type`,
            field_data_field_mother_disease_desc.`field_mother_disease_desc_value` `illness_description`,
            field_data_field_mother_disability.`field_mother_disability_value` `illness_prevent_movement`,
            field_data_field_mother_monthly_medecine.`field_mother_monthly_medecine_value` `need_monthly_treatment`,
            field_data_field_mother_gov_medecine.`field_mother_gov_medecine_value` `has_national_support`,
            field_data_field_mother_med_monthly_cost.`field_mother_med_monthly_cost_value` `treatment_monthly_amount`,
            field_data_field_mother_buy_med.`field_mother_buy_med_value` `treatment_affordable`,
            field_data_field_mother_needs_surgery.`field_mother_needs_surgery_value` `need_operation`
            FROM node
            LEFT JOIN field_data_field_related_name ON field_data_field_related_name.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_gender ON field_data_field_mother_gender.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_age ON field_data_field_mother_age.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_id_num ON field_data_field_mother_id_num.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_marital_status ON field_data_field_mother_marital_status.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_educ ON field_data_field_mother_educ.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_job ON field_data_field_mother_job.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_job_desc ON field_data_field_mother_job_desc.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_id_pic ON field_data_field_mother_id_pic.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_id_pic_back ON field_data_field_mother_id_pic_back.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_phone ON field_data_field_mother_phone.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_has_disease ON field_data_field_mother_has_disease.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_disease_type ON field_data_field_mother_disease_type.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_disease_desc ON field_data_field_mother_disease_desc.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_disability ON field_data_field_mother_disability.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_monthly_medecine ON field_data_field_mother_monthly_medecine.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_gov_medecine ON field_data_field_mother_gov_medecine.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_med_monthly_cost ON field_data_field_mother_med_monthly_cost.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_buy_med ON field_data_field_mother_buy_med.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_mother_needs_surgery ON field_data_field_mother_needs_surgery.`entity_id` = node.`nid`
            WHERE node.`type` = 'new_application' AND node.`nid` = ?",[$case_id]);  
            $data = json_decode(json_encode($data[0]), true);
            return $data;       
        
    }
    private function SelectCaseIncome($case_id)
    {
            $data = DB::connection('mysql')->select("SELECT '".$case_id."' as case_id,
            field_revision_field_income_monthly_value.`field_income_monthly_value_value` `monthly_amount`,
            field_data_field_income_source.`field_income_source_value` `source_type`,
            field_revision_field_source_state.`field_source_state_value` `source_flow`,
            field_revision_field_income_source_description.`field_income_source_description_value` `notes`
            FROM field_data_field_income_source
            LEFT JOIN field_revision_field_income_monthly_value ON field_revision_field_income_monthly_value.`entity_id` = field_data_field_income_source.`entity_id`
            LEFT JOIN field_revision_field_source_state ON field_revision_field_source_state.`entity_id` = field_data_field_income_source.`entity_id`
            LEFT JOIN field_revision_field_income_source_description ON field_revision_field_income_source_description.`entity_id` = field_data_field_income_source.`entity_id`
            WHERE field_data_field_income_source.entity_id  IN (SELECT field_sources_types_value FROM `field_data_field_sources_types` WHERE `field_data_field_sources_types`.`entity_id` = ?)",[$case_id]);  
            $data = json_decode(json_encode($data), true);
            return $data;       
        
    }
    private function SelectCaseSupport($case_id)
    {
            $data = DB::connection('mysql')->select("SELECT '".$case_id."' AS case_id,
            `field_data_field_select_type_of_help`.`field_select_type_of_help_value` `source_category`,
            `field_data_field_help_source_name`.`field_help_source_name_value` `source_name`,
            field_data_field_help_type.`field_help_type_value` `type` ,
            field_data_field_help_repitition.`field_help_repitition_value` `period`
                FROM field_data_field_type_of_help
                INNER JOIN field_data_field_select_type_of_help ON field_data_field_select_type_of_help.`entity_id` = field_data_field_type_of_help.`field_type_of_help_value`
                LEFT JOIN field_data_field_help_source_name ON field_data_field_help_source_name.`entity_id` = field_data_field_type_of_help.`field_type_of_help_value`
                LEFT JOIN field_data_field_help_type ON field_data_field_help_type.`entity_id` = field_data_field_type_of_help.`field_type_of_help_value`
                LEFT JOIN field_data_field_help_repitition ON field_data_field_help_repitition.`entity_id` = field_data_field_type_of_help.`field_type_of_help_value`
                WHERE `field_data_field_select_type_of_help`.entity_id IN (SELECT field_data_field_type_of_help.`field_type_of_help_value` FROM `field_data_field_type_of_help` WHERE `field_data_field_type_of_help`.`entity_id` = ?)",[$case_id]);   
            $data = json_decode(json_encode($data), true);     
            return $data;      
         
    }
    private function SelectCaseLoan($case_id)
    {
            $data = DB::connection('mysql')->select("SELECT '".$case_id."' AS case_id,
            `field_data_field_sub_loan_total`.`field_sub_loan_total_value`  amount,
            `field_data_field_sub_loan_remaining`.`field_sub_loan_remaining_value` stay,
            `field_data_field_sub_loan_reason`.`field_sub_loan_reason_value` reason,
            `field_data_field_sub_loan_paying_method`.`field_sub_loan_paying_method_value` refund_method,
            `field_data_field_sub_load_monthly_value`.`field_sub_load_monthly_value_value` monthly_amount
                FROM `field_data_field_sub_loan_collection`
                LEFT JOIN field_data_field_sub_loan_total ON field_data_field_sub_loan_total.`entity_id` = field_data_field_sub_loan_collection.`field_sub_loan_collection_value`
                LEFT JOIN field_data_field_sub_loan_remaining ON field_data_field_sub_loan_remaining.`entity_id` = field_data_field_sub_loan_collection.`field_sub_loan_collection_value`
                LEFT JOIN field_data_field_sub_loan_reason ON field_data_field_sub_loan_reason.`entity_id` = field_data_field_sub_loan_collection.`field_sub_loan_collection_value`
                LEFT JOIN field_data_field_sub_loan_paying_method ON field_data_field_sub_loan_paying_method.`entity_id` = field_data_field_sub_loan_collection.`field_sub_loan_collection_value`
                LEFT JOIN field_data_field_sub_load_monthly_value ON field_data_field_sub_load_monthly_value.`entity_id` = field_data_field_sub_loan_collection.`field_sub_loan_collection_value`
                WHERE field_data_field_sub_loan_total.entity_id IN (SELECT `field_sub_loan_collection_value` FROM `field_data_field_sub_loan_collection` WHERE `field_data_field_sub_loan_collection`.`entity_id` = ?)",[$case_id]);   
            $data = json_decode(json_encode($data), true);
            return $data;
         
    }
    private function SelectCaseRooms($case_id)
    {
            $data = DB::connection('mysql')->select("SELECT '".$case_id."' AS case_id,
            `field_data_field_room_type`.`field_room_type_value` `type`,
            `field_data_field_room_ceiling`.`field_room_ceiling_value` `roof_type`,
            `field_data_field_field_room_ceiling_state`.`field_field_room_ceiling_state_value` `roof_status`,
            `field_revision_field_room_painting`.`field_room_painting_value` `paint`,
            `field_revision_field_room_extra_details`.`field_room_extra_details_value` `notes`
                FROM `field_revision_field_rooms_details`
                LEFT JOIN field_data_field_room_type ON field_data_field_room_type.`entity_id` = field_revision_field_rooms_details.`field_rooms_details_value`
                LEFT JOIN field_data_field_room_ceiling ON field_data_field_room_ceiling.`entity_id` = field_revision_field_rooms_details.`field_rooms_details_value`
                LEFT JOIN field_data_field_field_room_ceiling_state ON field_data_field_field_room_ceiling_state.`entity_id` = field_revision_field_rooms_details.`field_rooms_details_value`
                LEFT JOIN field_revision_field_room_painting ON field_revision_field_room_painting.`entity_id` = field_revision_field_rooms_details.`field_rooms_details_value`
                LEFT JOIN field_revision_field_room_extra_details ON field_revision_field_room_extra_details.`entity_id` = field_revision_field_rooms_details.`field_rooms_details_value`
                WHERE field_data_field_room_type.entity_id IN (SELECT `field_rooms_details_value` FROM field_revision_field_rooms_details WHERE `field_revision_field_rooms_details`.`entity_id` = ?)",[$case_id]);   
            $data = json_decode(json_encode($data), true);
            return $data;
    }
    private function SelectCaseChilds($case_id)
    {
            $data = DB::connection('mysql')->select("SELECT '".$case_id."' AS case_id,
            `field_data_field_child_name`.`field_child_name_value` `name`,
            `field_data_field_gender`.`field_gender_value` gender ,
            `field_data_field_child_age`.`field_child_age_value` `age` ,
            `field_data_field_child_marital`.`field_child_marital_value` `relationship_status`,
            `field_data_field_learning_status`.`field_learning_status_value` `education_status`,
            `field_data_field_child_job`.`field_child_job_value` `work_status`,
            `field_data_field_child_job_desc`.`field_child_job_desc_value` `profession`,
            `field_data_field_child_have_desease`.`field_child_have_desease_value` `is_ill`,
            `field_data_field_child_disease_type`.`field_child_disease_type_value` `illness_type`,
            `field_data_field_child_disease_desc`.`field_child_disease_desc_value` `illness_description`,
            `field_data_field_child_disease_disability`.`field_child_disease_disability_value` `illness_prevent_movement`,
            `field_data_field_child_disease_monthly`.`field_child_disease_monthly_value` `need_monthly_treatment`,
            `field_data_field_child_disease_govern`.`field_child_disease_govern_value` `has_national_support`,
            `field_data_field_child_med_monthly_cost`.`field_child_med_monthly_cost_value` `treatment_monthly_amount`,
            `field_data_field_child_med_buy`.`field_child_med_buy_value` `treatment_affordable`,
            `field_data_field_child_need_surgery`.`field_child_need_surgery_value` `need_operation`
            FROM field_data_field_child_collection
            LEFT JOIN field_data_field_child_name ON field_data_field_child_name.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
            LEFT JOIN field_data_field_gender ON field_data_field_gender.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
            LEFT JOIN field_data_field_child_age ON field_data_field_child_age.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
            LEFT JOIN field_data_field_child_marital ON field_data_field_child_marital.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
            LEFT JOIN field_data_field_learning_status ON field_data_field_learning_status.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
            LEFT JOIN field_data_field_child_job ON field_data_field_child_job.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
            LEFT JOIN field_data_field_child_job_desc ON field_data_field_child_job_desc.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
            LEFT JOIN field_data_field_child_have_desease ON field_data_field_child_have_desease.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
    	    LEFT JOIN field_data_field_child_disease_govern ON field_data_field_child_disease_govern.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
    	    LEFT JOIN field_data_field_child_disease_type ON field_data_field_child_disease_type.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
    	    LEFT JOIN field_data_field_child_disease_desc ON field_data_field_child_disease_desc.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
    	    LEFT JOIN field_data_field_child_disease_disability ON field_data_field_child_disease_disability.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
    	    LEFT JOIN field_data_field_child_disease_monthly ON field_data_field_child_disease_monthly.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
    	    LEFT JOIN field_data_field_child_med_monthly_cost ON field_data_field_child_med_monthly_cost.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
    	    LEFT JOIN field_data_field_child_med_buy ON field_data_field_child_med_buy.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
    	    LEFT JOIN field_data_field_child_need_surgery ON field_data_field_child_need_surgery.`entity_id` = field_data_field_child_collection.`field_child_collection_value`
            WHERE field_data_field_child_name.entity_id IN (SELECT `field_child_collection_value` FROM `field_data_field_child_collection` WHERE `field_data_field_child_collection`.`entity_id` = ?)
            ",[$case_id]);   
            $data = json_decode(json_encode($data), true);
            return $data;
        
    }
    private function SelectCaseRoommates($case_id)
    {
            $data = DB::connection('mysql')->select("
            SELECT '".$case_id."' AS case_id,
            `field_data_field_relative_name`.`field_relative_name_value` `name`,
            `field_data_field_relative_gender`.`field_relative_gender_value` gender ,
            `field_data_field_relative_age`.`field_relative_age_value` `age` ,
            `field_data_field_relative_marital`.`field_relative_marital_value` `relationship_status`,
            `field_data_field_relative_education`.`field_relative_education_value` `education_status`,
            `field_data_field_relatives_job`.`field_relatives_job_value` `work_status`,
            `field_data_field_relative_job_desc`.`field_relative_job_desc_value` `profession`,
            `field_data_field_relative_disease`.`field_relative_disease_value` `is_ill`,
            `field_data_field_relative_disease_type`.`field_relative_disease_type_value` `illness_type`,
            `field_data_field_relative_disease_desc`.`field_relative_disease_desc_value` `illness_description`,
            `field_data_field_relatives_disability`.`field_relatives_disability_value` `illness_prevent_movement`,
            `field_data_field_relatives_monthly_med`.`field_relatives_monthly_med_value` `need_monthly_treatment`,
            `field_data_field_relatives_gov_med`.`field_relatives_gov_med_value` `has_national_support`,
            `field_data_field_relative_monthly_cost`.`field_relative_monthly_cost_value` `treatment_monthly_amount`,
            `field_data_field_relative_buy_disease`.`field_relative_buy_disease_value` `treatment_affordable`,
            `field_data_field_relative_surgery`.`field_relative_surgery_value` `need_operation`
            FROM field_data_field_relatives_info
            LEFT JOIN field_data_field_relative_name ON field_data_field_relative_name.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relative_gender ON field_data_field_relative_gender.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relative_age ON field_data_field_relative_age.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relative_marital ON field_data_field_relative_marital.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relative_education ON field_data_field_relative_education.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relatives_job ON field_data_field_relatives_job.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relative_job_desc ON field_data_field_relative_job_desc.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relative_disease ON field_data_field_relative_disease.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relatives_gov_med ON field_data_field_relatives_gov_med.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relative_disease_type ON field_data_field_relative_disease_type.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relative_disease_desc ON field_data_field_relative_disease_desc.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relatives_disability ON field_data_field_relatives_disability.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relatives_monthly_med ON field_data_field_relatives_monthly_med.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relative_monthly_cost ON field_data_field_relative_monthly_cost.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relative_buy_disease ON field_data_field_relative_buy_disease.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            LEFT JOIN field_data_field_relative_surgery ON field_data_field_relative_surgery.`entity_id` = field_data_field_relatives_info.`field_relatives_info_value`
            WHERE field_data_field_relative_name.entity_id IN (SELECT field_data_field_relatives_info.`field_relatives_info_value` FROM `field_data_field_relatives_info` WHERE `field_data_field_relatives_info`.`entity_id` = ?)",[$case_id]);   
            $data = json_decode(json_encode($data), true);
            return $data;
    }
    private function GetAllCases($start,$limit)
    {
        $cases = DB::connection('mysql')->select("SELECT node.nid case_id FROM node WHERE node.`type` = 'new_application'");
         // LIMIT ?,?",[$start,$limit]);
        return $cases;
    }
    private function SelectCase($case_id)
    {
        $cases_part1 = DB::connection('mysql')->select("SELECT 
        '".$case_id."' id,
        /* field_data_field_app_state.`field_app_state_value` `status`, */
        field_data_field_researcher_name.`field_researcher_name_value` `researcher_name`,
        field_data_field_city.`field_city_value` `governorate`,
        field_data_field_markaz.`field_markaz_tid` `city`,
        field_data_field_the_village.`field_the_village_tid` `district`,
        field_data_field_related.`field_related_tid` `following` ,
        field_data_field_app_date.`field_app_date_value` `real_date`,
        `node`.`title` `name`,
        field_data_field_gender.`field_gender_value` `gender`,
        field_data_field_father_age.`field_father_age_value` `age`,
        field_data_field_father_id_num.`field_father_id_num_value` `national_id`,
        field_data_field_marital_status.`field_marital_status_value` `relationship_status`,
        field_data_field_father_educ.`field_father_educ_value` `education_status`,
        field_data_field_father_job.`field_father_job_value` `work_status`,
        field_data_field_father_job_desc.`field_father_job_desc_value` `profession`,
        field_data_field_father_id_pic.`field_father_id_pic_fid` `national_id_front`,
        field_data_field_father_id_pic_back.`field_father_id_pic_back_fid` `national_id_back`,
        field_data_field_father_phone.`field_father_phone_value` `phone`,
        field_data_field_father_health.`field_father_health_value` `is_ill`,
        field_data_field_type_diseases.`field_type_diseases_value` `illness_type`,
        field_data_field_father_disease_desc.`field_father_disease_desc_value` `illness_description`,
        field_data_field_father_disability.`field_father_disability_value` `illness_prevent_movement`,
        field_data_field_father_monthly_medicine.`field_father_monthly_medicine_value` `need_monthly_treatment`,
        field_data_field_father_gov_medecine.`field_father_gov_medecine_value` `has_national_support`,
        field_data_field_father_monthly_medecine.`field_father_monthly_medecine_value` `treatment_monthly_amount`,
        field_data_field_father_buy_medecine.`field_father_buy_medecine_value` `treatment_affordable`,
        field_data_field_father_surgery.`field_father_surgery_value` `need_operation`,
        field_data_field_total_family_income.`field_total_family_income_value` `income_amount`,
        field_data_field_income_range.`field_income_range_value` `income_amount_category`,
        field_data_field_income_src_num.`field_income_src_num_value` `income_source_count`,
        field_data_field_num_helps.`field_num_helps_value` `support_count`,
        field_data_field_debit_range.`field_debit_range_value` `debts_total_range`,
        field_data_field_loan_total.`field_loan_total_value` `debts_total`,
        field_data_field_expenses_home_rent.`field_expenses_home_rent_value` `expenses_house_rent`,
        field_data_field_expenses_land_rent.`field_expenses_land_rent_value` `expenses_farm_rent`,
        field_data_field_expenses_medicine.`field_expenses_medicine_value` `expenses_treatment` ,
        field_data_field_expenses_cleaners.`field_expenses_cleaners_value` `expenses_detergent`,
        field_data_field_expenses_schools.`field_expenses_schools_value` `expenses_school_subscription`,
        field_data_field_expenses_child_lessons.`field_expenses_child_lessons_value` `expenses_child_course`,
        field_data_field_expenses_water.`field_expenses_water_value` `expenses_water_receipt`,
        field_data_field_expenses_electricity.`field_expenses_electricity_value` `expenses_electricity_receipt`,
        field_data_field_expenses_clothes.`field_expenses_clothes_value` `expenses_clothes`,
        field_data_field_expenses_phone_bills.`field_expenses_phone_bills_value` `expenses_phone_credit`,
        field_data_field_expenses_loans.`field_expenses_loans_value` `expenses_debts`,
        field_data_field_expenses_transportation.`field_expenses_transportation_value` `expenses_transportation`,
        field_data_field_expenses_animals_food.`field_expenses_animals_food_value` `expenses_pets_food`,
        field_data_field_expenses_smoke.`field_expenses_smoke_value` `expenses_smoking`,
        field_data_field_expenses_lunch.`field_expenses_lunch_value` `expenses_food`,
        field_data_field_expenses_others.`field_expenses_others_value` `expenses_other`,
        field_data_field_expenses_range.`field_expenses_range_value` `expenses_total_category`,
        field_data_field_total_expenses_manual.`field_total_expenses_manual_value` `expenses_total`,
        field_data_field_living_in.`field_living_in_value` `assets_house_type` ,
        field_data_field_home_rent_or_owner.`field_home_rent_or_owner_value` `assets_house_status`,
        field_data_field_home_electricity_meter.`field_home_electricity_meter_value` `assets_electric_meter`,
        field_data_field_home_water_meter.`field_home_water_meter_value` `assets_water_meter`,
        field_data_field_water_meter_source.`field_water_meter_source_value` `assets_water_alternative`,
        field_data_field_do_have_farm.`field_do_have_farm_value` `assets_farm`,
        field_data_field_birds_animals.`field_birds_animals_value` `assets_pets`,
        field_data_field_other_home.`field_other_home_value` `assets_house_alternative_status`
        
    
        FROM node
        LEFT JOIN field_data_field_app_state ON field_data_field_app_state.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_researcher_name ON field_data_field_researcher_name.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_city ON field_data_field_city.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_markaz ON field_data_field_markaz.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_the_village ON field_data_field_the_village.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_related ON field_data_field_related.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_app_date ON field_data_field_app_date.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_gender ON field_data_field_gender.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_age ON field_data_field_father_age.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_id_num ON field_data_field_father_id_num.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_marital_status ON field_data_field_marital_status.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_educ ON field_data_field_father_educ.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_job ON field_data_field_father_job.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_job_desc ON field_data_field_father_job_desc.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_id_pic ON field_data_field_father_id_pic.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_id_pic_back ON field_data_field_father_id_pic_back.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_phone ON field_data_field_father_phone.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_health ON field_data_field_father_health.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_type_diseases ON field_data_field_type_diseases.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_disease_desc ON field_data_field_father_disease_desc.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_disability ON field_data_field_father_disability.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_monthly_medicine ON field_data_field_father_monthly_medicine.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_gov_medecine ON field_data_field_father_gov_medecine.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_monthly_medecine ON field_data_field_father_monthly_medecine.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_buy_medecine ON field_data_field_father_buy_medecine.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_father_surgery ON field_data_field_father_surgery.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_sun ON field_data_field_sun.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_relatives_staying ON field_data_field_relatives_staying.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_total_family_income ON field_data_field_total_family_income.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_income_range ON field_data_field_income_range.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_income_src_num ON field_data_field_income_src_num.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_num_helps ON field_data_field_num_helps.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_debit_range ON field_data_field_debit_range.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_loan_total ON field_data_field_loan_total.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_home_rent ON field_data_field_expenses_home_rent.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_land_rent ON field_data_field_expenses_land_rent.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_medicine ON field_data_field_expenses_medicine.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_schools ON field_data_field_expenses_schools.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_child_lessons ON field_data_field_expenses_child_lessons.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_water ON field_data_field_expenses_water.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_electricity ON field_data_field_expenses_electricity.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_clothes ON field_data_field_expenses_clothes.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_phone_bills ON field_data_field_expenses_phone_bills.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_loans ON field_data_field_expenses_loans.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_transportation ON field_data_field_expenses_transportation.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_animals_food ON field_data_field_expenses_animals_food.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_cleaners ON field_data_field_expenses_cleaners.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_smoke ON field_data_field_expenses_smoke.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_lunch ON field_data_field_expenses_lunch.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_others ON field_data_field_expenses_others.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_expenses_range ON field_data_field_expenses_range.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_total_expenses_manual ON field_data_field_total_expenses_manual.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_living_in ON field_data_field_living_in.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_rent_or_owner ON field_data_field_home_rent_or_owner.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_electricity_meter ON field_data_field_home_electricity_meter.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_water_meter ON field_data_field_home_water_meter.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_water_meter_source ON field_data_field_water_meter_source.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_do_have_farm ON field_data_field_do_have_farm.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_birds_animals ON field_data_field_birds_animals.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_other_home ON field_data_field_other_home.`entity_id` = node.`nid`
        WHERE node.`type` = 'new_application' AND node.`nid` = ?
        ",[$case_id]);
        $cases_part2 = DB::connection('mysql')->select("SELECT 
        field_data_field_who_lives_in_it.`field_who_lives_in_it_value` `assets_house_alternative_resident`,
        field_data_field_why_not_living_there.`field_why_not_living_there_value` `assets_house_alternative_leave`,
        field_data_field_home_from.`field_home_from_value` `assets_house_construction`,
        field_data_field_house_floors.`field_house_floors_value` `assets_house_floors_count`,
        field_data_field_house_room_number.`field_house_room_number_value` `assets_house_rooms_count`,
        field_data_field_have_bathroom.`field_have_bathroom_value` `has_bathroom`,
        field_data_field_bathroom_door.`field_bathroom_door_value` `bathroom_has_door`,
        field_data_field_has_sink.`field_has_sink_value` `bathroom_has_basin`,
        field_data_field_bath_base.`field_bath_base_value` `bathroom_has_toilet`,
        field_data_field_bathroom_cieling.`field_bathroom_cieling_value` `bathroom_roof`,
        field_data_field_bath_wall.`field_bath_wall_value` `bathroom_wall`,
        field_data_field_bath_floor.`field_bath_floor_value` `bathroom_floor`,
        field_data_field_mattress.`field_mattress_value` `amenities_mattress`,
        field_data_field_beds.`field_beds_value` `amenities_bed`,
        field_data_field_home_fans.`field_home_fans_value` `amenities_fans`,
        field_data_field_home_blankets.`field_home_blankets_value` `amenities_blankets`,
        field_data_field_home_cooker.`field_home_cooker_value` `amenities_stove`,
        field_data_field_has_oven.`field_has_oven_value` `amenities_oven`,
        field_data_field_home_flat_cooker.`field_home_flat_cooker_value` `amenities_flame`,
        field_data_field_home_heater.`field_home_heater_value` `amenities_heater`,
        field_data_field_home_refreg.`field_home_refreg_value` `amenities_fridge`,
        field_data_field_home_washing_machine.`field_home_washing_machine_value` `amenities_washer`,
        field_data_field_home_waredrobe.`field_home_waredrobe_value` `amenities_cupbord`,
        field_data_field_home_cupboard.`field_home_cupboard_value` `amenities_nish`,
        field_data_field_home_sofas.`field_home_sofas_value` `amenities_arika`,
        field_data_field_home_table.`field_home_table_value` `amenities_table`,
        field_data_field_home_tv.`field_home_tv_value` `amenities_television`,
        field_data_field_home_satalite.`field_home_satalite_value` `amenities_receiver`,
        field_data_field_home_computer.`field_home_computer_value` `amenities_computer`,
        field_data_field_water_meter.`field_water_meter_value` `need_water`,
        field_data_field_bath_room.`field_bath_room_value` `need_bathroom`,
        field_data_field_roof.`field_roof_value` `need_roof`,
        field_data_field_blankets.`field_blankets_value` `need_blankets`,
        field_data_field_build_walls.`field_build_walls_value` `need_construction`,
        field_data_field_education.`field_education_value` `need_education`,
        field_data_field_no_hungar.`field_no_hungar_value` `need_food`,
        field_data_field_extra_notes.`field_extra_notes_value` `additional_notes`
        
        FROM node
        LEFT JOIN field_data_field_who_lives_in_it ON field_data_field_who_lives_in_it.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_why_not_living_there ON field_data_field_why_not_living_there.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_from ON field_data_field_home_from.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_house_floors ON field_data_field_house_floors.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_house_room_number ON field_data_field_house_room_number.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_have_bathroom ON field_data_field_have_bathroom.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_bathroom_door ON field_data_field_bathroom_door.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_has_sink ON field_data_field_has_sink.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_bath_base ON field_data_field_bath_base.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_bathroom_cieling ON field_data_field_bathroom_cieling.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_bath_wall ON field_data_field_bath_wall.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_bath_floor ON field_data_field_bath_floor.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_mattress ON field_data_field_mattress.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_beds ON field_data_field_beds.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_fans ON field_data_field_home_fans.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_blankets ON field_data_field_home_blankets.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_cooker ON field_data_field_home_cooker.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_has_oven ON field_data_field_has_oven.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_heater ON field_data_field_home_heater.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_refreg ON field_data_field_home_refreg.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_washing_machine ON field_data_field_home_washing_machine.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_flat_cooker ON field_data_field_home_flat_cooker.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_waredrobe ON field_data_field_home_waredrobe.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_cupboard ON field_data_field_home_cupboard.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_table ON field_data_field_home_table.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_sofas ON field_data_field_home_sofas.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_tv ON field_data_field_home_tv.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_satalite ON field_data_field_home_satalite.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_home_computer ON field_data_field_home_computer.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_water_meter ON field_data_field_water_meter.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_bath_room ON field_data_field_bath_room.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_roof ON field_data_field_roof.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_blankets ON field_data_field_blankets.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_build_walls ON field_data_field_build_walls.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_education ON field_data_field_education.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_no_hungar ON field_data_field_no_hungar.`entity_id` = node.`nid`
        LEFT JOIN field_data_field_extra_notes ON field_data_field_extra_notes.`entity_id` = node.`nid`
        WHERE node.`type` = 'new_application' AND node.`nid` = ?
        ",[$case_id]);
        return array_merge((array)($cases_part1[0]) + (array)($cases_part2[0]));
    }

    private function SelectCaseStatus($case_id)
    {
        $data = DB::connection('mysql')->select("SELECT 
            node.`nid` As case_id,
            field_data_field_app_state.`field_app_state_value` `status`,
            field_data_field_app_date.`field_app_date_value` `date`
            FROM node
            LEFT JOIN field_data_field_app_state ON field_data_field_app_state.`entity_id` = node.`nid`
            LEFT JOIN field_data_field_app_date ON field_data_field_app_date.`entity_id` = node.`nid`
            
            WHERE node.`type` = 'new_application' AND node.`nid` = ? and field_data_field_app_date.`field_app_date_value` is not null
            ",[$case_id]);    

        foreach ($data as $key => $status) {
            if($status->status == 1)
                $status->status = 'لم يتم البدئ في التنفيذ';
            if($status->status == 2)
                $status->status = 'جاري تجميع الملفات';
            if($status->status == 3)
                $status->status = 'جاري التنفيذ';
            if($status->status == 4)
                $status->status = 'تم دفع الرسوم';
            if($status->status == 5)
                $status->status = 'تم تقديم الملفات';
            if($status->status == 6)
                $status->status = 'تم تجميع الملفات';
            if($status->status == 7)
                $status->status = 'تم تنفيذ ادخال عداد';
            if($status->status == 8)
                $status->status = 'توصيل المياه';
            if($status->status == 9)
                $status->status = 'بناء دورة مياه';
            if($status->status == 10)
                $status->status = 'تم تنفيذ بناء دورة مياه';
            if($status->status == 11)
                $status->status = 'تم تنفيذ سقف';
            if($status->status == 12)
                $status->status = 'تم تنفيذ توزيع بطاطين';
            if($status->status == 13)
                $status->status = 'تم تنفيذ مشروع نفسي اتعلم';
            if($status->status == 14)
                $status->status = 'تم تنفيذ مشروع لا للجوع';
            if($status->status == 15)
                $status->status = 'بناء جدران (جزء من البيت)';
        }

        $data = json_decode(json_encode($data), true);
        return $data;
    }

}
