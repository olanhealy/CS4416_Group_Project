<?php
enum CourseOfStudy: string
{
    case Architecture = "Architecture";
    case ARTS = "Arts";
    case ECONOMICS = "Economics";
    case ENGLISH = "English";
    case FRENCH = "French";
    case GAEILGE = "Gaeilge";
    case GEOGRAPHY = "Geography";
    case GERMAN = "German";
    case HISTORY = "History";
    case MUSIC_AND_DANCE = "Music and Dance";
    case LINGUISTICS = "Linguistics";
    case MATHEMATICS = "Mathematics";
    case DIGITAL_CULTURE_AND_COMMUNICATIONS = "Digital Culture and Communications";
    case POLITICS_AND_INTERNATIONAL_RELATIONS = "Politics and International Relations";
    case PSYCHOLOGY = "Psychology";
    case PUBLIC_ADMINISTRATION = "Public Administration";
    case SOCIOLOGY = "Sociology";
    case SPANISH = "Spanish";
    case APPLIED_LANGUAGES = "Applied Languages";
    case CONTEMPORARY_DANCE = "Contemporary Dance";
    case CRIMINAL_JUSTICE = "Criminal Justice";
    case EUROPEAN_STUDIES = "European Studies";
    case INTERNATIONAL_BUSINESS = "International Business";
    case IRISH_DANCE = "Irish Dance";
    case IRISH_MUSIC = "Irish Music";
    case JOURNALISM_AND_DIGITAL_COMMUNICATION = "Journalism and Digital Communication";
    case LAW_AND_ACCOUNTING = "Law and Accounting";
    case PSYCHOLOGY_AND_SOCIOLOGY = "Psychology and Sociology";
    case VOICE = "Voice";
    case WORLD_MUSIC = "World Music";
    case BUISNESS_STUDIES = "Business Studies";
    case LANGUAGES = "Languages";
    case CHEMICAL_AND_BIOCHEMICAL_ENGINEERING = "Chemical & Biochemical Engineering";
    case LAW = "Law";
    case LAW_PLUS = "Law Plus";
    case COMMON_AND_CIVIL_LAW = "Common and Civil Law";
    case MEDICINE = "Medicine";
    case MATHEMATICS_AND_COMPUTER_SCIENCE_TEACHING = "Mathematics and Computer Science Teaching";
    case CONSTRUCTION_MANAGEMENT_AND_ENGINEERING = "Construction Management and Engineering";
    case ENVIRONMENTAL_SCEINCE = "Environmental Science";
    case EQUINE_SCIENCE = "Equine Science";
    case EXERCISE_AND_HEALTH_FITNESS_MANAGEMENT = "Exercise & Health Fitness Management";
    case FINANCIAL_MATHEMATICS = "Financial Mathematics";
    case FOOD_SCIENCE_AND_HEALTH = "Food Science and Health";
    case INTERACTION_DESIGN = "Interaction Design";
    case MIDWIFERY = "Midwifery";
    case MUSIC_MEDIA_AND_PERFORMACE_TECHNOLOGY = "Music, Media & Performance Technology";
    case GENERAL_NURSING = "Nursing (General)";
    case INTELLECTUAL_DISABILITY_NURSING = "Nursing (Intellectual Disability)";
    case MENTAL_HEALTH_NURSING = "Nursing (Mental Health)";
    case OCCUPATIONAL_THERAPY = "Occupational Therapy";
    case PARAMEDIC_STUDIES = "Paramedic Studies";
    case PHYSICAL_EDUCATION = "Physical Education";
    case PHYSIOTHERAPY = "Physiotherapy";
    case PRODUCT_DESIGN_AND_TECHNOLOGY = "Product Design and Technology";
    case SOCIAL_SCIENCES = "Social Sciences";
    case SPORT_AND_EXERCISE_SCIENCES = "Sport and Exercise Sciences";
    case TECHNOLOGY_MANAGEMENT = "Technology Management";
    case BIOLOGY_TEACHING = "Biology Teaching";
    case CHEMISTRY_TEACHING = "Chemistry Teaching";
    case PHYSICS_TEACHING = "Physics Teaching";
    case AGRICULTURAL_TEACHING = "Agricultural Teaching";
    case PHYSICS_AND_CHEMISTRY_TEACHING = "Physics and Chemistry Teaching";
    case GRAPHICS_AND_CONSTRUCTION_TECHNOLOGY_TEACHING = "Graphics and Construction Technology Teaching";
    case GRAPHICS_ENGINEERING_AND_TECHNOLOGY_TEACHING = "Graphics, Engineering and Technology Teaching";
    case ELECTRICAL_ENGINEERING = "Electrical Engineering";
    case ELECTRONIC_AND_COMPUTER_ENGINEERING = "Electronic and Computer Engineering";
    case ARTIFICIAL_INTELLIGENCE_AND_MACHINE_LEARNING = "Artificial Intelligence and Machine Learning";
    case IMMERSIVE_SOFTWARE_ENGINEERING = "Immersive Software Engineering";
    case AERONAUTICAL_ENGINEERING = "Aeronautical Engineering";
    case BIOLOGICAL_AND_CHEMICAL_SCEINCES = "Biological and Chemical Sciences (Common Entry)";
    case BIOSIENCE = "Bioscience";
    case INDUSTRIAL_BIOCHEMISTRY = "Industrial Biochemistry";
    case PHARMACEUTICAL_AND_INDUSTRIAL_CHEMISTRY = "Pharmaceutical and Industrial Chemistry";
    case BIOMEDICAL_SCIENCE = "Biomedical Science";
    case COMPUTER_SCEINCE = "Computer Science";
    case COMPUTER_SYSTEMS = "Computer Systems";
    case Games_DEVELOPMENT = "Games Development";
    case CYBER_SECURITY_AND_IT_FORENSICS = "Cyber Security and IT Forensics";
    case ENGINEERING = "Engineering";
    case BIOMEDICAL_ENGINEERING = "Biomedical Engineering";
    case CIVIL_ENGINEERING = "Civil Engineering";
    case DESIGN_AND_MANUFACTURE = "Design and Manufacture";
    case MECHANICAL_ENGINEERING = "Mechanical Engineering";
    case DIGITAL_MECHATRONIC_ENGINEERING = "Digital Mechatronic Engineering";
    case MATHEMATICAL_SCIENCES = "Mathematical Sceinces";
    case MATHEMATICS_AND_PHYSICS = "Mathematics and Physics";
    case ECONOMICS_AND_MATHEMATICS = "Economics and Mathematics";
    case PHYSICS = "Physics";
    case APPLIED_PHYSICS = "Applied Physics";

}

$options = '';
foreach (CourseOfStudy::cases() as $case) {
    $value = $case->value;
    $selected = ($_SESSION['existingCourse'] === $value) ? "selected" : "";
    $options .= "<option value=\"$value\" $selected>" . $value . "</option>";
}
?>