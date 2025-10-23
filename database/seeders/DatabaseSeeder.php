<?php

namespace Database\Seeders;

use App\Models\DietaryCondition;
use App\Models\DocumentType;
use App\Models\Gender;
use App\Models\GynecoObstetricCondition;
use App\Models\MedicationCondition;
use App\Models\PathologicalCondition;
use App\Models\Permission;
use App\Models\Role;
use App\Models\ToxicologicalCondition;
use App\Models\TreatmentCondition;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::firstOrCreate(['name' => 'SUPER_ADMIN']);
        $ownerRole = Role::firstOrCreate(['name' => 'OWNER']);
        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $employeeRole = Role::firstOrCreate(['name' => 'EMPLOYEE']);
        $patientRole = Role::firstOrCreate(['name' => 'PATIENT']);

        $adminUser1 = User::create([
            'name' => 'ViverClinic Admin',
            'email' => 'viverclinicadmin@viverclinic.com',
            'password' => Hash::make('Yt7C5sj91c51hAQbYMQM'),
        ]);

        $adminUser1->assignRole('SUPER_ADMIN');

        $adminUser2 = User::create([
            'name' => 'Xavier',
            'email' => '1@1.com',
            'password' => Hash::make('1'),
        ]);

        $adminUser2->assignRole('SUPER_ADMIN');

        $ownerRole->givePermissionTo(Permission::firstOrCreate(['name' => 'owner_dashboard']));
        $ownerRole->givePermissionTo(Permission::firstOrCreate(['name' => 'owner_dashboard_role_management']));
        $ownerRole->givePermissionTo(Permission::firstOrCreate(['name' => 'owner_dashboard_user_management']));
        $ownerRole->givePermissionTo(Permission::firstOrCreate(['name' => 'owner_dashboard_branch_management']));

        $adminRole->givePermissionTo(Permission::firstOrCreate(['name' => 'admin_dashboard']));
        $adminRole->givePermissionTo(Permission::firstOrCreate(['name' => 'admin_dashboard_role_management']));
        $adminRole->givePermissionTo(Permission::firstOrCreate(['name' => 'admin_dashboard_user_management']));
        $adminRole->givePermissionTo(Permission::firstOrCreate(['name' => 'admin_dashboard_branch_management']));

        $employeeRole->givePermissionTo(Permission::firstOrCreate(['name' => 'employee_dashboard']));
        $employeeRole->givePermissionTo(Permission::firstOrCreate(['name' => 'employee_agenda_day_home_btn']));
        $employeeRole->givePermissionTo(Permission::firstOrCreate(['name' => 'employee_agenda_new_home_btn']));
        $employeeRole->givePermissionTo(Permission::firstOrCreate(['name' => 'employee_job_training_home_btn']));
        $employeeRole->givePermissionTo(Permission::firstOrCreate(['name' => 'employee_promotions_home_btn']));

        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_dashboard']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_medical_record_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_qualify_staff_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_treatment_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_care_tips_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_buy_package_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_virtual_wallet_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_promotions_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_recomentations_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_referrals_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_schedule_appointment_home_btn']));
        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'patient_cancel_appointment_home_btn']));

        Gender::create(['name' => 'Masculine','code' => 'M','status' => '1']);
        Gender::create(['name' => 'Female','code' => 'F','status' => '1']);

        DocumentType::create(['name' => 'Citizenship Card','status' => '1']);
        DocumentType::create(['name' => 'Foreigners Identity Card','status' => '1']);
        DocumentType::create(['name' => 'Passport','status' => '1']);
        DocumentType::create(['name' => 'Identity card','status' => '1']);

        PathologicalCondition::create(['name' => 'Cholesterol','status' => '1']);
        PathologicalCondition::create(['name' => 'Autoimmune Disease','status' => '1']);
        PathologicalCondition::create(['name' => 'You suffer from heart disease','status' => '1']);
        PathologicalCondition::create(['name' => 'Allergies','status' => '1']);
        PathologicalCondition::create(['name' => 'High Blood Pressure','status' => '1']);
        PathologicalCondition::create(['name' => 'Diabetes','status' => '1']);
        PathologicalCondition::create(['name' => 'Varicose veins','status' => '1']);
        PathologicalCondition::create(['name' => 'Migraines','status' => '1']);
        PathologicalCondition::create(['name' => 'Thyroid','status' => '1']);
        PathologicalCondition::create(['name' => 'Gastrointestinal problems','status' => '1']);
        PathologicalCondition::create(['name' => 'Joint Pain','status' => '1']);
        PathologicalCondition::create(['name' => 'Fluid retention','status' => '1']);
        PathologicalCondition::create(['name' => 'None of the above','status' => '1']);
        PathologicalCondition::create(['name' => '2 or more of the above','status' => '1']);

        ToxicologicalCondition::create(['name' => 'You consume liquor','status' => '1']);
        ToxicologicalCondition::create(['name' => 'You use drugs','status' => '1']);
        ToxicologicalCondition::create(['name' => 'You smoke','status' => '1']);
        ToxicologicalCondition::create(['name' => 'None of the above','status' => '1']);
        ToxicologicalCondition::create(['name' => '2 or more of the above','status' => '1']);

        GynecoObstetricCondition::create(['name' => 'Polycystic ovaries', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Endometriosis', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Uterine fibroids', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Endocrine disorders', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Sexually transmitted infections', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Urinary tract infections', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Pelvic inflammatory disease', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Inguinal hernias', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Epididymitis', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Hydrocele', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Varicocele', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Testicular injuries', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Inflammation of the penis', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'None of the above', 'status' => 1]);

        MedicationCondition::create(['name' => 'Analgesics and anti-inflammatories, such as Ibuprofen and Naproxen', 'status' => 1]);
        MedicationCondition::create(['name' => 'Antacids, such as Omeprazole or Ranitidine', 'status' => 1]);
        MedicationCondition::create(['name' => 'Antibiotics, such as Ciprofloxacin, Azithromycin, or Gentamicin', 'status' => 1]);
        MedicationCondition::create(['name' => 'Antihistamines, such as Ebastine, Loratadine or Claritin', 'status' => 1]);
        MedicationCondition::create(['name' => 'Antidepressants, such as Amitriptyline, Bromazepam, or Fluoxetine', 'status' => 1]);
        MedicationCondition::create(['name' => 'Diuretics, such as Cyclothiazide or Quinetazone', 'status' => 1]);
        MedicationCondition::create(['name' => 'Corticosteroids, such as Prednisone or Hydrocortisone', 'status' => 1]);
        MedicationCondition::create(['name' => 'To lower cholesterol, such as Lovastatin or Atorvastatin', 'status' => 1]);
        MedicationCondition::create(['name' => 'To combat acne, such as Roaccutane (Isotretinoin)', 'status' => 1]);
        MedicationCondition::create(['name' => 'None of the above', 'status' => 1]);
        MedicationCondition::create(['name' => '2 or more of the above', 'status' => 1]);

        DietaryCondition::create(['name' => 'You consume foods high in sugar', 'status' => 1]);
        DietaryCondition::create(['name' => 'You consume ultra-processed foods', 'status' => 1]);
        DietaryCondition::create(['name' => 'You have a special diet', 'status' => 1]);
        DietaryCondition::create(['name' => 'You do intermittent fasting', 'status' => 1]);
        DietaryCondition::create(['name' => 'You consume dairy products', 'status' => 1]);
        DietaryCondition::create(['name' => '2 more options apply to me', 'status' => 1]);
        DietaryCondition::create(['name' => 'None of the above', 'status' => 1]);

        TreatmentCondition::create(['name' => 'Laser hair removal', 'status' => 1, 'terms_conditions' => '<section><h2>Consentimiento informado —  Depilación Láser</h2><ol><li><p>REQUIERO Y AUTORIZO al Centro “VIVERCLINIC”, para que me realicen el tratamiento de Depilación láser. En términos generales, el propósito del procedimiento es eliminar el vello, despigmentar mi zona y estimular el colágeno.</p></li><li><p>CONFIRMO que el centro de estética “VIVERCLINIC” me ha explicado detalladamente, en palabras comprensibles para mí, el efecto y la naturaleza del/los tratamiento(s) a efectuar y las molestias que puedo sentir durante el/los procedimiento(s).</p><p>Con este tratamiento puedo experimentar molestias, dolores corporales leves; si mi piel es muy sensible pueden salir irritaciones y algunas alteraciones en la piel como enrojecimiento, picazón o sensibilidad. En caso extremo, si es muy sensible, algún tipo de quemadura. Han sido contestadas todas mis preguntas que libremente he formulado acerca del tratamiento.</p></li><li><p>COMPRENDO que si me voy a realizar algún tipo de tratamiento láser debo ser consciente de que todos los metabolismos son diferentes; para mejores resultados deberé seguir las indicaciones. El centro de estética recomienda 10 sesiones para lograr la inactivación temporal del vello; como explicamos anteriormente cada metabolismo es muy diferente, el grosor del vello y la zona son factores que también influyen. Por eso hay pacientes que pueden requerir sesiones de refuerzo luego de las 10 sesiones ya realizadas para completar el proceso, las cuales se deben asumir por parte del paciente.</p></li><li><p>DEBO INFORMAR A LA PROFESIONAL SI PADEZCO ALGÚN PROBLEMA DE ALERGIA EN LA PIEL, ALGUNA ENFERMEDAD HORMONAL O DE BASE; ESTO SERÁ DE SUMA IMPORTANCIA ANTES, DURANTE Y DESPUÉS DEL PROCEDIMIENTO, Y PERMITIRÁ QUE LA PROFESIONAL CUMPLA LOS PROTOCOLOS SEGÚN LA INFORMACIÓN QUE LE BRINDE. DE NO SER ASÍ, EL CENTRO DE ESTÉTICA NO SE HACE RESPONSABLE, NO GARANTIZA RESULTADOS Y NO HACE DEVOLUCIÓN DEL DINERO.</p><p>Es normal que durante el procedimiento puedas sentir molestias dependiendo del fototipo de piel, umbral de dolor y otros factores externos; la aplicación de productos con alcohol o ácidos pueden causar irritaciones y afectar el proceso. El centro de estética cuenta con una experiencia de más de 10 años, con tecnologías actualizadas y protocolos para obtener resultados exitosos. Recuerda que ningún proceso de depilación láser es definitivo: lo que hacemos es inactivar el vello por cierto periodo de tiempo; existen pacientes a quienes el vello nunca más les vuelve a salir, pero la gran mayoría requerirá mantenimiento en algún momento, esto puede ser cada año o cada 4 años (el mantenimiento es sobre unos cuantos vellos muy finos y delgados que comienzan a salir). El mantenimiento por lo general son 2 a 3 sesiones. Que tu tratamiento sea más efectivo depende de los siguientes factores:</p><ol><li>Cómo accione el láser en tu cuerpo (depende de tu salud, color de piel, grosor del vello).</li><li>Si tienes o no enfermedades de base.</li><li>Si tienes alteraciones metabólicas.</li><li>La puntualidad con tus citas.</li><li>La preparación y cuidados posteriores al tratamiento.</li></ol></li><li><p>El centro de estética no garantiza resultados ya que la depilación láser depende en un 50% de cómo lleves el proceso y cómo esté tu metabolismo, y en un 50% de la tecnología y nuestro personal (aclarando que tenemos más de mil pacientes con procesos exitosos). Nuestras técnicas y tecnologías dan resultados exitosos; el restante depende de tu estado metabólico.</p><p>Recuerda que para asistir a tus citas deben aparecer en tu perfil los pagos efectuados en sus respectivas fechas o, si separaste alguna promoción, el abono debe aparecer en tu perfil. De no ser así el centro de estética no se hace responsable; por eso es muy importante que al momento de abonar verifiques en tu perfil que dicho pago fue efectuado y quedó registrado.</p><p>Bajo ninguna circunstancia puedes ofrecer a nuestras profesionales dinero bajo cuerda para realizarte otras zonas del cuerpo, ya que tenemos un control interno exhaustivo; en caso de hacerlo estarías cometiendo un delito. Este documento es un respaldo legal que haremos valer ante las respectivas autoridades; las acciones legales tomadas por el centro de estética pueden incluir cárcel, por lo que te solicitamos abstenerte de hacerlo.</p><p>En el caso de que algún paciente se sobrepase o insinúe actos sexuales y morbosos con nuestras empleadas, el centro de estética no le prestará más el servicio y la empleada tendrá la libertad de iniciar procesos legales contra la persona.</p><p>Si no asistes a tu cita, si no llegas puntual o si no cancelas con anticipación la cita en caso de no poder asistir, se tomará como asistida.</p><p>Acepto que el centro de estética VIVERCLINIC me realice la depilación láser con sus protocolos y tecnologías.</p></li><li><p>El fin del procedimiento que he solicitado tiene como objetivo mejorar mi apariencia física.</p></li><li><p>CONSIENTO en ser fotografiado o filmado antes, durante y después del tratamiento; este material será un medio gráfico de diagnóstico y de registro para mi historia clínica, propiedad del centro de estética “VIVERCLINIC”, pudiendo ser publicado en revistas, publicidad o redes sociales. Se entiende que en cualquier uso no seré identificado con mi nombre ni se mostrará mi rostro, a no ser que lo autorice.</p><p>ES OBLIGACIÓN asistir cada 20 días mínimo y máximo cada 30 días a las sesiones; de esto dependerán que mis resultados sean exitosos. Si nos pasamos del tiempo nuestro proceso se ve afectado y puede tocar iniciarlo nuevamente o realizar sesiones extras. En caso de no poder asistir a mi cita debo informar y cancelar con 1 día de anticipación. El tiempo de los demás es valioso; me comprometo a respetar los horarios. Si llego tarde a mi sesión esperaré a que el profesional tenga espacio disponible para la atención.</p></li><li><p>ME COMPROMETO a seguir fielmente, en la mejor medida de mis posibilidades, las instrucciones impartidas por “VIVERCLINIC” para antes, durante y después del tratamiento mencionadas para mejores resultados.</p></li><li><p>DEBO cancelar la sesión de mi tratamiento antes de ingresar a la cabina y RECUERDO TRAER BIEN ASEADA LA ZONA QUE SE VA A TRATAR.</p></li><li><p>DECLARO:</p><ul><li>Que cumplo con todas las indicaciones dadas previamente a realizarme el tratamiento.</li><li>No me encuentro en estado de embarazo.</li><li>Fui sincera con toda la información e historia clínica que me solicitó el centro de estética; declaro que soy una persona apta para el proceso de depilación láser.</li></ul></li></ol></section>']);
        TreatmentCondition::create(['name' => 'Reduction', 'status' => 1, 'terms_conditions' => '<section><h2>Consentimiento informado — Reducción</h2><ol><li><p><strong>REQUIERO Y AUTORIZO</strong> al centro de estética Viverclinic para que me realicen el tratamiento de reducción de medidas y tonificación corporal. En términos generales, el propósito del procedimiento es mejorar mi apariencia física.</p></li><li><p><strong>CONFIRMO</strong> que el centro de estética Viverclinic me ha explicado detalladamente, en palabras comprensibles para mí, el efecto y la naturaleza del/los tratamiento(s) a efectuar y las molestias que puedo sentir durante el/los procedimiento(s). Con este tratamiento puedo experimentar molestias, dolores corporales leves; si mi piel es muy sensible me pueden salir morados, irritaciones y algunas alteraciones en la piel como enrojecimiento, picazón o sensibilidad o, en caso extremo, algún tipo de quemadura. Se recomienda luego del procedimiento beber buena cantidad de agua, realizar masajes en la zona donde se realiza el tratamiento, hacer ejercicio y no utilizar productos que puedan irritar la piel. Han sido contestadas todas mis preguntas que libremente he formulado acerca del tratamiento.</p></li><li><p><strong>COMPRENDO</strong> que si me voy a realizar algún tipo de tratamiento de reducción soy consciente de que es una ayuda, y que todos los metabolismos son diferentes; para mejores resultados deberé seguir estas indicaciones:</p><ul><li>Hacer cardio 3 días a la semana, mínimo media hora.</li><li>Beber 1 litro de agua diario.</li><li>Seguir la dieta nutricional suministrada.</li><li>Asistir a las citas asignadas del tratamiento.</li></ul><p>Durante el procedimiento puedo sentir un poco de dolor por causa de los masajes que son muy importantes durante los tratamientos.</p></li><li><p>El fin del procedimiento que he solicitado tiene como objetivo mejorar mi apariencia física y mejorar mis hábitos alimenticios.</p></li><li><p><strong>CONSIENTO</strong> en ser fotografiado o filmado antes, durante y después del tratamiento, siendo este material un medio gráfico de diagnóstico y de registro para mi historia clínica, propiedad del centro de estética Viverclinic, pudiendo ser publicado en revistas, publicidad o redes sociales. Se entiende específicamente que en cualquier uso no seré identificado con mi nombre y no se enseñará mi rostro a no ser que sea autorizado. <strong>COMPRENDO</strong> la importancia de asistir 2 veces por semana a las sesiones; de esto dependerán mis resultados. En caso de no poder asistir debo informar y cancelar la cita con 1 día de anticipación; de no ser así se tomará como asistida. El tiempo de los demás es muy valioso, por eso me comprometo a respetar los horarios. Si llego tarde a mi sesión se continuará la cita, pero se dará inicio en el horario en que llegue el paciente y finalizará en el mismo horario establecido desde un inicio. En caso de cancelar la cita el mismo día se tomará como asistida.</p></li><li><p><strong>ME COMPROMETO</strong> a seguir fielmente, en la mejor medida de mis posibilidades, las instrucciones impartidas por Viverclinic para antes, durante y después del tratamiento mencionadas para mejores resultados. <strong>DEBO INFORMAR A LA PROFESIONAL SI PADEZCO ALGÚN PROBLEMA DE ALERGIA EN LA PIEL, ALGUNA ENFERMEDAD HORMONAL O DE BASE</strong>; ESTO SERÁ DE SUMA IMPORTANCIA ANTES, DURANTE Y DESPUÉS DEL PROCEDIMIENTO. ESTO PERMITIRÁ QUE LA PROFESIONAL CUMPLA LOS PROTOCOLOS SEGÚN LA INFORMACIÓN QUE TÚ LE BRINDES. DE NO SER ASÍ, EL CENTRO DE ESTÉTICA NO SE HACE RESPONSABLE, NO GARANTIZA RESULTADOS Y NO HACE DEVOLUCIÓN DEL DINERO.</p></li></ol><p>El centro de estética no garantiza resultados ya que cualquier tratamiento estético corporal depende un 40% del tratamiento y un 60% de los cuidados y hábitos que tenga el paciente posterior a la sesión. Nuestro tratamiento de reducción, tonificación y moldeamiento corporal lo realizamos con tecnologías de última generación no invasivas; realizamos drenajes linfáticos y la cita tiene una duración aproximada de 1 hora.</p><p>Recuerda que para asistir a tus citas deben aparecer en tu perfil los pagos efectuados en sus respectivas fechas o, si separaste alguna promoción, el abono debe aparecer en tu perfil; de no ser así el centro de estética no se hace responsable. Por eso es muy importante que, al momento de abonar dineros, verifiques en tu perfil que dicho pago se efectuó y quedó registrado.</p><p>Bajo ninguna circunstancia puedes ofrecer a nuestras profesionales dinero bajo cuerda para realizarte otras zonas del cuerpo, ya que tenemos un control interno muy exhaustivo; en caso de hacerlo deberás tener presente que estás cometiendo un delito. Este documento es un respaldo legal el cual haremos valer ante las respectivas autoridades. Las acciones legales tomadas por el centro de estética incluyen cárcel, así que por tu bienestar te solicitamos abstenerte de hacerlo.</p><p>En el caso de que algún paciente se sobrepase o insinúe actos sexuales y morbosos con nuestras empleadas, el centro de estética no le prestará más el servicio y la empleada tendrá la libertad de iniciar procesos legales en contra de la persona. Si no asistes a tu cita, si no llegas puntual o si no cancelas con anticipación la cita en caso de no poder asistir, se tomará como asistida.</p><p>Acepto que el centro de estética Viverclinic me realice la depilación láser con sus protocolos y tecnologías.</p><ol start="10"><li><p>El fin del procedimiento que he solicitado tiene como objetivo mejorar mi apariencia física.</p></li><li><p><strong>CONSIENTO</strong> en ser fotografiado o filmado antes, durante y después del tratamiento, siendo este material un medio gráfico de diagnóstico y de registro para mi historia clínica, propiedad del centro de estética Viverclinic, pudiendo ser publicado en revistas, publicidad o redes sociales. Se entiende específicamente que en cualquier uso no seré identificado con mi nombre y no se enseñará mi rostro a no ser que sea autorizado. <strong>ES OBLIGACIÓN</strong> asistir 1 o 2 veces por semana; de esto dependerá que mis resultados sean exitosos. Una vez nos pasamos del tiempo nuestro proceso se daña automáticamente y toca iniciarlo nuevamente o realizar sesiones extras. En caso de no poder asistir a mi cita debo informar y cancelar la cita con 1 día de anticipación. El tiempo de los demás es muy valioso, por eso me comprometo a respetar los horarios. Si llego tarde a mi sesión voy a esperar que el profesional tenga espacio disponible para la atención.</p></li><li><p><strong>ME COMPROMETO</strong> a seguir fielmente, en la mejor medida de mis posibilidades, las instrucciones impartidas por Viverclinic para antes, durante y después del tratamiento mencionadas para mejores resultados.</p></li></ol><p><strong>DECLARO:</strong></p><ul><li>No estar tomando medicación alguna que modere mi estado de inmunidad.</li><li>No encontrarme en estado de embarazo.</li><li>No poseer implantes metálicos en mi cuerpo (prótesis de huesos u otros).</li><li>No poseer marcapasos cardíaco.</li></ul></section>']);

        $client01 = User::create([
            'name' => 'cliente01',
            'email' => 'c01@1.com',
            'password' => Hash::make('1'),
            'birthday' => '2025-10-29',
            'gender_id' => 1,
            'informed_consent' => 0,
            'citizenship' => 'test',
            'document_type_id' => 1,
            'document_number' => 'test',
            'profession' => 'test',
            'phone' => '123456798',
            'address' => 'test',
            'surgery' => 'test',
            'recommendation' => 'test',
            'terms_conditions' => 1,
            'directory' => null,
            'photo_profile' => null,
            'not_pregnant' => 1,
            'pathological_id' => 1,
            'toxicological_id' => 1,
            'gyneco_obstetric_id' => 1,
            'medication_id' => 1,
            'dietary_id' => 1,
            'treatment_id' => 1,
        ]);

        $client01->assignRole('PATIENT');

    }
}
