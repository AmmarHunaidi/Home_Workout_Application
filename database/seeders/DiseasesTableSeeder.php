<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DiseasesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('diseases')->delete();

        \DB::table('diseases')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Leukemia',
                'created_at' => '2022-07-07 12:32:51',
                'updated_at' => '2022-07-07 12:32:52',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Lymphoma',
                'created_at' => '2022-07-07 12:32:52',
                'updated_at' => '2022-07-07 12:32:52',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Breast cancer',
                'created_at' => '2022-07-07 12:33:16',
                'updated_at' => '2022-07-07 12:33:16',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'Rheumatoid arthritis',
                'created_at' => '2022-07-07 12:33:55',
                'updated_at' => '2022-07-07 12:33:55',
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'cancer',
                'created_at' => '2022-07-09 01:31:13',
                'updated_at' => '2022-07-09 01:31:13',
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'Multiple sclerosis',
                'created_at' => '2022-07-09 01:34:16',
                'updated_at' => '2022-07-09 01:34:16',
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'Lung cancer',
                'created_at' => '2022-07-09 01:34:16',
                'updated_at' => '2022-07-09 01:34:16',
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'Inflammatory',
                'created_at' => '2022-08-10 13:45:29',
                'updated_at' => '2022-08-10 13:45:29',
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'Osteoarthritis',
                'created_at' => '2022-08-10 13:45:57',
                'updated_at' => '2022-08-10 13:45:57',
            ),
            9 =>
            array(
                'id' => 10,
                'name' => 'Pelvic inflammatory disease (PID)',
                'created_at' => '2022-08-10 13:45:57',
                'updated_at' => '2022-08-10 13:45:57',
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'Viral meningitis',
                'created_at' => '2022-08-10 13:46:50',
                'updated_at' => '2022-08-10 13:46:50',
            ),
            11 =>
            array(
                'id' => 12,
                'name' => 'Atherosclerosis',
                'created_at' => '2022-08-10 13:47:10',
                'updated_at' => '2022-08-10 13:47:10',
            ),
            12 =>
            array(
                'id' => 13,
                'name' => 'Fibromyalgia',
                'created_at' => '2022-08-10 13:47:46',
                'updated_at' => '2022-08-10 13:47:46',
            ),
            13 =>
            array(
                'id' => 14,
                'name' => 'Infectious diseases',
                'created_at' => '2022-08-10 13:47:46',
                'updated_at' => '2022-08-10 13:47:46',
            ),
            14 =>
            array(
                'id' => 15,
                'name' => 'HIV',
                'created_at' => '2022-08-10 13:48:45',
                'updated_at' => '2022-08-10 13:48:45',
            ),
            15 =>
            array(
                'id' => 16,
                'name' => 'Hepatitis C',
                'created_at' => '2022-08-10 13:49:01',
                'updated_at' => '2022-08-10 13:49:01',
            ),
            16 =>
            array(
                'id' => 17,
                'name' => 'Zika virus',
                'created_at' => '2022-08-10 13:49:14',
                'updated_at' => '2022-08-10 13:49:14',
            ),
            17 =>
            array(
                'id' => 18,
                'name' => 'Tuberculosis',
                'created_at' => '2022-08-10 13:49:38',
                'updated_at' => '2022-08-10 13:49:38',
            ),
            18 =>
            array(
                'id' => 19,
                'name' => 'Influenza',
                'created_at' => '2022-08-10 13:49:46',
                'updated_at' => '2022-08-10 13:49:46',
            ),
            19 =>
            array(
                'id' => 20,
                'name' => 'Irritable bowel syndrome (IBS)',
                'created_at' => '2022-08-10 13:50:02',
                'updated_at' => '2022-08-10 13:50:02',
            ),
            20 =>
            array(
                'id' => 21,
                'name' => 'Chronic fatigue syndrome (CFS)',
                'created_at' => '2022-08-10 13:50:20',
                'updated_at' => '2022-08-10 13:50:20',
            ),
            21 =>
            array(
                'id' => 22,
                'name' => 'Temporomandibular joint pain (TMJ)',
                'created_at' => '2022-08-10 13:50:35',
                'updated_at' => '2022-08-10 13:50:35',
            ),
            22 =>
            array(
                'id' => 23,
                'name' => 'Gastroesophageal reflux disorder (GERD)',
                'created_at' => '2022-08-10 13:51:14',
                'updated_at' => '2022-08-10 13:51:14',
            ),
            23 =>
            array(
                'id' => 24,
                'name' => 'Interstitial cystitis',
                'created_at' => '2022-08-10 13:51:30',
                'updated_at' => '2022-08-10 13:51:30',
            ),
            24 =>
            array(
                'id' => 25,
                'name' => 'Acute Flaccid Myelitis (AFM)',
                'created_at' => '2022-08-10 13:53:12',
                'updated_at' => '2022-08-10 13:53:12',
            ),
            25 =>
            array(
                'id' => 26,
                'name' => 'Alphaviruses',
                'created_at' => '2022-08-10 13:53:12',
                'updated_at' => '2022-08-10 13:53:12',
            ),
            26 =>
            array(
                'id' => 27,
                'name' => 'Arthritis',
                'created_at' => '2022-08-10 13:54:27',
                'updated_at' => '2022-08-10 13:54:27',
            ),
            27 =>
            array(
                'id' => 28,
                'name' => 'Babesiosis',
                'created_at' => '2022-08-10 13:54:27',
                'updated_at' => '2022-08-10 13:54:27',
            ),
            28 =>
            array(
                'id' => 29,
                'name' => 'Endometrial Cancer',
                'created_at' => '2022-08-10 13:55:00',
                'updated_at' => '2022-08-10 13:55:00',
            ),
            29 =>
            array(
                'id' => 30,
                'name' => 'Celiac Disease',
                'created_at' => '2022-08-10 13:56:19',
                'updated_at' => '2022-08-10 13:56:19',
            ),
            30 =>
            array(
                'id' => 31,
                'name' => 'Chickenpox',
                'created_at' => '2022-08-10 13:56:46',
                'updated_at' => '2022-08-10 13:56:46',
            ),
            31 =>
            array(
                'id' => 32,
                'name' => 'Chronic Obstructive Pulmonary Disease (COPD)',
                'created_at' => '2022-08-10 13:56:55',
                'updated_at' => '2022-08-10 13:56:55',
            ),
            32 =>
            array(
                'id' => 33,
                'name' => 'Diabetes',
                'created_at' => '2022-08-10 13:57:19',
                'updated_at' => '2022-08-10 13:57:19',
            ),
            33 =>
            array(
                'id' => 34,
                'name' => 'Arboviral Encephalitis',
                'created_at' => '2022-08-10 13:57:51',
                'updated_at' => '2022-08-10 13:57:51',
            ),
            34 =>
            array(
                'id' => 35,
                'name' => 'Asthma',
                'created_at' => '2022-08-10 13:58:23',
                'updated_at' => '2022-08-10 13:58:23',
            ),
            35 =>
            array(
                'id' => 36,
                'name' => 'Carbapenem-Resistant Enterobacteriaceae (CRE) Infections',
                'created_at' => '2022-08-10 13:58:43',
                'updated_at' => '2022-08-10 13:58:43',
            ),
            36 =>
            array(
                'id' => 37,
                'name' => 'Bacterial Vaginosis',
                'created_at' => '2022-08-10 13:59:11',
                'updated_at' => '2022-08-10 13:59:11',
            ),
            37 =>
            array(
                'id' => 38,
                'name' => 'Cardiovascular Disease',
                'created_at' => '2022-08-10 13:59:28',
                'updated_at' => '2022-08-10 13:59:28',
            ),
            38 =>
            array(
                'id' => 39,
                'name' => 'Chagas Disease',
                'created_at' => '2022-08-10 13:59:55',
                'updated_at' => '2022-08-10 13:59:55',
            ),
            39 =>
            array(
                'id' => 40,
                'name' => 'Diphtheria',
                'created_at' => '2022-08-10 14:00:19',
                'updated_at' => '2022-08-10 14:00:19',
            ),
            40 =>
            array(
                'id' => 41,
                'name' => 'Ebola Virus',
                'created_at' => '2022-08-10 14:01:06',
                'updated_at' => '2022-08-10 14:01:06',
            ),
            41 =>
            array(
                'id' => 42,
                'name' => 'Gonorrhea',
                'created_at' => '2022-08-10 14:01:29',
                'updated_at' => '2022-08-10 14:01:29',
            ),
            42 =>
            array(
                'id' => 43,
                'name' => 'Group A Streptococcus',
                'created_at' => '2022-08-10 14:02:06',
                'updated_at' => '2022-08-10 14:02:06',
            ),
            43 =>
            array(
                'id' => 44,
                'name' => 'Haff Disease',
                'created_at' => '2022-08-10 14:02:33',
                'updated_at' => '2022-08-10 14:02:33',
            ),
            44 =>
            array(
                'id' => 45,
                'name' => 'Headaches',
                'created_at' => '2022-08-10 14:02:55',
                'updated_at' => '2022-08-10 14:02:55',
            ),
            45 =>
            array(
                'id' => 46,
                'name' => 'Heat-related Illnesses',
                'created_at' => '2022-08-10 14:03:26',
                'updated_at' => '2022-08-10 14:03:26',
            ),
            46 =>
            array(
                'id' => 47,
                'name' => 'E. Coli',
                'created_at' => '2022-08-10 14:03:35',
                'updated_at' => '2022-08-10 14:03:35',
            ),
            47 =>
            array(
                'id' => 48,
                'name' => 'Flu',
                'created_at' => '2022-08-10 14:04:33',
                'updated_at' => '2022-08-10 14:04:33',
            ),
            48 =>
            array(
                'id' => 49,
                'name' => 'Gout',
                'created_at' => '2022-08-10 14:04:33',
                'updated_at' => '2022-08-10 14:04:33',
            ),
            49 =>
            array(
                'id' => 50,
                'name' => 'Hantaviruses',
                'created_at' => '2022-08-10 14:04:58',
                'updated_at' => '2022-08-10 14:04:58',
            ),
            50 =>
            array(
                'id' => 51,
                'name' => 'High Blood Pressure',
                'created_at' => '2022-08-10 14:05:21',
                'updated_at' => '2022-08-10 14:05:21',
            ),
            51 =>
            array(
                'id' => 52,
                'name' => 'Incontinence',
                'created_at' => '2022-08-10 14:05:46',
                'updated_at' => '2022-08-10 14:05:46',
            ),
            52 =>
            array(
                'id' => 53,
                'name' => 'Lead Poisoning',
                'created_at' => '2022-08-10 14:06:07',
                'updated_at' => '2022-08-10 14:06:07',
            ),
            53 =>
            array(
                'id' => 54,
                'name' => 'Leptospirosis',
                'created_at' => '2022-08-10 14:06:25',
                'updated_at' => '2022-08-10 14:06:25',
            ),
            54 =>
            array(
                'id' => 55,
                'name' => 'Lyme Disease',
                'created_at' => '2022-08-10 14:07:02',
                'updated_at' => '2022-08-10 14:07:02',
            ),
            55 =>
            array(
                'id' => 56,
                'name' => 'Molluscum Contagiosum',
                'created_at' => '2022-08-10 14:07:41',
                'updated_at' => '2022-08-10 14:07:41',
            ),
            56 =>
            array(
                'id' => 57,
                'name' => 'Mumps',
                'created_at' => '2022-08-10 14:08:17',
                'updated_at' => '2022-08-10 14:08:17',
            ),
            57 =>
            array(
                'id' => 58,
                'name' => 'Polio',
                'created_at' => '2022-08-10 14:08:32',
                'updated_at' => '2022-08-10 14:08:32',
            ),
            58 =>
            array(
                'id' => 59,
                'name' => 'Parkinson\'s Disease',
                'created_at' => '2022-08-10 14:08:51',
                'updated_at' => '2022-08-10 14:08:51',
            ),
            59 =>
            array(
                'id' => 60,
                'name' => 'Monkeypox',
                'created_at' => '2022-08-10 14:09:04',
                'updated_at' => '2022-08-10 14:09:04',
            ),
            60 =>
            array(
                'id' => 61,
                'name' => 'Non-Gonococcal Urethritis (NGU)',
                'created_at' => '2022-08-10 14:09:41',
                'updated_at' => '2022-08-10 14:09:41',
            ),
            61 =>
            array(
                'id' => 62,
                'name' => 'Pertussis',
                'created_at' => '2022-08-10 14:10:22',
                'updated_at' => '2022-08-10 14:10:22',
            ),
            62 =>
            array(
                'id' => 63,
                'name' => 'Rabies',
                'created_at' => '2022-08-10 14:11:38',
                'updated_at' => '2022-08-10 14:11:38',
            ),
            63 =>
            array(
                'id' => 64,
                'name' => 'Rubella',
                'created_at' => '2022-08-10 14:12:09',
                'updated_at' => '2022-08-10 14:12:09',
            ),
            64 =>
            array(
                'id' => 65,
                'name' => 'SARS (Healthcare Providers)',
                'created_at' => '2022-08-10 14:12:29',
                'updated_at' => '2022-08-10 14:12:29',
            ),
            65 =>
            array(
                'id' => 66,
                'name' => 'Shigellosis',
                'created_at' => '2022-08-10 14:12:49',
                'updated_at' => '2022-08-10 14:12:49',
            ),
            66 =>
            array(
                'id' => 67,
                'name' => 'Tetanus',
                'created_at' => '2022-08-10 14:13:11',
                'updated_at' => '2022-08-10 14:13:11',
            ),
            67 =>
            array(
                'id' => 68,
                'name' => 'Stroke',
                'created_at' => '2022-08-10 14:13:31',
                'updated_at' => '2022-08-10 14:13:31',
            ),
            68 =>
            array(
                'id' => 69,
                'name' => 'Syphilis',
                'created_at' => '2022-08-10 14:14:09',
                'updated_at' => '2022-08-10 14:14:09',
            ),
            69 =>
            array(
                'id' => 70,
                'name' => 'Yellow Fever',
                'created_at' => '2022-08-10 14:23:00',
                'updated_at' => '2022-08-10 14:23:00',
            ),
            70 =>
            array(
                'id' => 71,
                'name' => 'cholesterol',
                'created_at' => '2022-08-10 14:23:00',
                'updated_at' => '2022-08-10 14:23:00',
            ),
            71 =>
            array(
                'id' => 72,
                'name' => 'vertebrae pinch',
                'created_at' => '2022-08-10 14:24:40',
                'updated_at' => '2022-08-10 14:24:40',
            ),
            72 =>
            array(
                'id' => 73,
                'name' => 'disc',
                'created_at' => '2022-08-10 14:24:40',
                'updated_at' => '2022-08-10 14:24:40',
            ),
        ));
    }
}
