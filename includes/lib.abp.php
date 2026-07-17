<?php

/** Module & menu functions **/

function get_active_menu($filename) {

    $filename = pathinfo($filename, PATHINFO_BASENAME);

    return query_select_single_value("menuParentKey", TABLE_MODULES, "moduloFiles LIKE '%$filename%'");

}

# get modules
function get_modules() {
    return sql_select("SELECT * FROM ".TABLE_MODULES." ORDER BY orden ASC");
}

function get_module($id) {
    return sql_select_row("SELECT * FROM ".TABLE_MODULES." WHERE moduloId = $id");
}


/** Role functions **/

# get roles
function get_roles() {
    return sql_select("SELECT * FROM ".TABLE_ROLES);
}

function get_role($id) {
    return sql_select_row("SELECT * FROM ".TABLE_ROLES." WHERE rolId = $id");
}

function get_role_permissions($roleId) {

    $modules = get_modules();

    for($m=0; $m<count($modules); $m++) {

        $perms[$m]['moduloId'] = $modules[$m]['moduloId'];
        $perms[$m]['modulo'] = $modules[$m]['modulo'];

        $mod_perm = sql_select("SELECT permisoId, permisoKey, permiso FROM ".TABLE_MODULES_PERMS." WHERE moduloId = ".$modules[$m]['moduloId']." ORDER BY permiso ASC");

        if($mod_perm) {
            for($i=0; $i<count($mod_perm); $i++) {
                $rol_perm = sql_select_row("SELECT rolPermisoId FROM ".TABLE_ROLES_PERMS." WHERE rolid = $roleId AND permisoId = ".$mod_perm[$i]['permisoId']);
                if($rol_perm) {
                    $perms[$m]['perms'][] = array("permisoId" => $mod_perm[$i]['permisoId'], "permiso" => $mod_perm[$i]['permiso'], "check" => true);
                } else {
                    $perms[$m]['perms'][] = array("permisoId" => $mod_perm[$i]['permisoId'], "permiso" => $mod_perm[$i]['permiso'], "check" => false);
                }
            }
        } else {
            $perms[$m]['perms'] = array();
        }

    }

    return $perms;

}

# get header menu items based on role, modules & permissions
function role_get_menu($roleId) {

    $menu = sql_select("SELECT DISTINCT menuParentKey, menuParentName 
                        FROM ".TABLE_MODULES." m, ".TABLE_MODULES_PERMS." p, ".TABLE_ROLES_PERMS." rp, ".TABLE_ROLES." r 
                        WHERE m.moduloId = p.moduloId AND p.permisoId = rp.permisoId AND rp.rolId = r.rolId 
                            AND r.rolId = ".session_get_data("roleId")." AND p.permisoKey = 'READ' 
                        ORDER BY m.orden ASC");

    if($menu) {

        for($i=0; $i<count($menu); $i++) {

            $items = sql_select("SELECT m.modulo, m.menuFile  
                                FROM ".TABLE_MODULES." m, ".TABLE_MODULES_PERMS." p, ".TABLE_ROLES_PERMS." rp, ".TABLE_ROLES." r 
                                WHERE m.moduloId = p.moduloId AND p.permisoId = rp.permisoId AND rp.rolId = r.rolId 
                                    AND m.menuParentKey = '".$menu[$i]['menuParentKey']."' AND r.rolId = ".session_get_data("roleId")." AND p.permisoKey = 'READ' 
                                ORDER BY m.orden ASC");
            for($j=0; $j<count($items); $j++) {
                $menu[$i]['items'][] = array("url" => $items[$j]['menuFile'], "item" => $items[$j]['modulo']);
            }

        }

    }

    return $menu;

}



/** Permissions functions **/

# get permissions
function get_permissions() {
    return sql_select("SELECT p.*, m.modulo FROM ".TABLE_MODULES_PERMS." p, ".TABLE_MODULES." m WHERE p.moduloId = m.moduloId");
}
# get permission
function get_permission($id) {
    return sql_select_row("SELECT p.*, m.modulo FROM ".TABLE_MODULES." m, ".TABLE_MODULES_PERMS." p  WHERE m.moduloId = p.moduloId AND permisoId = $id");
}


/** Company functions **/

function get_companies() {
    return sql_select("SELECT * FROM ".TABLE_COMPANIES);
}

function get_company_info($company_id) {

    $result = sql_select_row("  SELECT c.*, sr.claveRegimen, sr.regimen, CONCAT(sr.claveRegimen, ' - ', sr.regimen) AS regimenFiscal 
                                FROM ".TABLE_COMPANIES." c, ".TABLE_SAT_REGIMEN_FISCAL." sr 
                                WHERE c.regimenId = sr.regimenId AND c.companyId = $company_id");
    $info = json_decode($result['info'], true);
    unset($result['info']);

    return array_merge($result, $info);

}

function get_user_companies() {
    if(session_get_data("roleId")!=ROLE_VENDOR) {
        return sql_select("SELECT c.* FROM ".TABLE_COMPANIES." c, ".TABLE_USERS_COMPANIES." uc WHERE c.companyId = uc.companyId AND uc.usuarioId = ".session_get_data("userId")." ORDER BY c.companyId ASC");
    }
    return false;
}

function get_user_company() {
    $result = sql_select_row("SELECT * FROM ".TABLE_COMPANIES." WHERE companyId = ".session_get_data("companyId"));
    if($result) {
        $info = json_decode($result['info'], true);
        unset($result['info']);
        return array_merge($result, $info);
    } else {
        return $result;
    }
}

function get_user_in_company($company_id, $companies) {
    if(var_is_valid_array($companies)) {
        foreach($companies as $c) {
            if($c['companyId']==$company_id) {
                return true;
            }
        }
    }
    return false;
}

/** Budgets functions **/

function get_budgets_all() {
    return sql_select("SELECT   b.presupuestoId, b.fechaDeRodaje, p.proyectoId, p.titulo, 
                                c.razonSocial, d.directorNombre, CONCAT(p.titulo, ' - ', LPAD(b.numero, 2, '0')) AS referencia 
                        FROM ".TABLE_PROJECTS_BUDGETS." b, ".TABLE_PROJECTS." p, ".TABLE_CUSTOMERS." c, ".TABLE_DIRECTORS." d 
                        WHERE b.proyectoId = p.proyectoId AND p.cuentaId = c.cuentaId AND p.directorId = d.directorId AND p.deleted = 0 AND p.activo = 1");
}

function get_budgets($projectId, $customerId, $directorId, $dateFrom, $dateTo) {

    $sql_project = "";
    if($projectId>0) {
        $sql_project = " AND b.proyectoId = $projectId";
    }

    $sql_customer = "";
    if($customerId>0) {
        $sql_customer = " AND p.cuentaId = $customerId";
    }

    $sql_director = "";
    if($directorId>0) {
        $sql_director = " AND b.directorId = $directorId";
    }

    $sql_date = " AND b.fechaDeRodaje BETWEEN '$dateFrom' AND '$dateTo'";

    return sql_select(" SELECT  b.presupuestoId, b.diasFilmacion, b.fechaDeRodaje, p.proyectoId, 
                                p.titulo, c.razonSocial, d.directorNombre, CONCAT(p.titulo, ' - ', LPAD(b.numero, 2, '0')) AS referencia, 
                                SUM(bi.total) AS total 
                        FROM ".TABLE_PROJECTS_BUDGETS." b, ".TABLE_PROJECTS." p, ".TABLE_CUSTOMERS." c, ".TABLE_DIRECTORS." d, ".TABLE_PROJECTS_BUDGETS_ITEMS." bi 
                        WHERE b.proyectoId = p.proyectoId AND p.cuentaId = c.cuentaId AND b.directorId = d.directorId AND b.presupuestoId = bi.presupuestoId AND 
                            p.deleted = 0 AND p.activo = 1
                            $sql_project $sql_customer $sql_director $sql_date 
                        GROUP BY b.presupuestoId
                    ");

}

function get_budget($budgetId) {
    return sql_select_row("SELECT *, LPAD(numero, 2, '0') AS referencia FROM ".TABLE_PROJECTS_BUDGETS." WHERE presupuestoId = $budgetId");
}

function get_budget_item_id($budgetId, $concepto) {

    $concepto = explode(" ", $concepto);

    if(is_array($concepto) && isset($concepto[0]) && (int)trim($concepto[0])>0) {
        return (int)query_select_single_value("itemId", TABLE_PROJECTS_BUDGETS_ITEMS, "presupuestoId = $budgetId AND cuenta = ".(int)trim($concepto[0]));
    } else {
        return (int)query_select_single_value("itemId", TABLE_PROJECTS_BUDGETS_ITEMS, "presupuestoId = $budgetId");
    }

}

# get budget's next numero
function get_budget_name($budgetId) {

    return query_select_single_value(	"CONCAT(p.proyectoId, '.', pp.numero, ' ', p.titulo)", 
                                        TABLE_PROJECTS." p, ".TABLE_PROJECTS_BUDGETS." pp", 
                                        "p.proyectoId = pp.proyectoId AND pp.presupuestoId = ".$budgetId
                                    );

}

function get_budget_next_num($projectId) {
    
    $num = (int)query_select_single_value("(numero+1) AS numero", TABLE_PROJECTS_BUDGETS, "proyectoId = $projectId", "numero DESC");

    if($num==0) {
        return 1;
    } else {
        return $num;
    }

}

function get_budget_items($budgetId, $selectedId=0) {

    return sql_select("SELECT i.itemId, i.presupuestoId, i.conceptoId, i.parentId, i.categoria, i.nivel, CONCAT(i.cuenta, ' - ', i.nombre) AS concepto, i.moneda 
                        FROM ".TABLE_PROJECTS_BUDGETS_ITEMS." i
                        WHERE i.presupuestoId = $budgetId 
                        ORDER BY i.cuenta ASC, i.categoria DESC, i.nivel ASC");
    
}

# get project's budget select options
function get_budget_options($budgetId, $selectedId) {

    $options = "";
    $results = sql_select("SELECT itemId, conceptoId, categoria, nivel, CONCAT(cuenta, ' - ', nombre) AS concepto
                            FROM ".TABLE_PROJECTS_BUDGETS_ITEMS." WHERE presupuestoId = $budgetId AND nivel < 2 
                            ORDER BY cuenta ASC, categoria DESC, nivel ASC");
    
    if($results) {
        for($i=0; $i<count($results); $i++) {
            if($results[$i]['conceptoId']==$selectedId) {
                $options .= '<option value="'.$results[$i]['conceptoId'].'" selected>'.space_by_level($results[$i]['nivel']).$results[$i]['concepto']."</option>\n";
            } else {
                $options .= '<option value="'.$results[$i]['conceptoId'].'">'.space_by_level($results[$i]['nivel']).$results[$i]['concepto']."</option>\n";
            }
        }
    }

    return $options;

}

# get budgets table rows
function get_budget_table_rows($budgetId, $monedas) {

    $rows = "";
    $results = sql_select("SELECT * FROM ".TABLE_PROJECTS_BUDGETS_ITEMS." 
                            WHERE presupuestoId = $budgetId 
                            ORDER BY cuenta ASC, categoria DESC, nivel ASC, itemId ASC");

    if(is_array($results) && count($results)>0) {

        $budget_total = 0;
        
        for($i=0; $i<count($results); $i++) {

            $itemId = $results[$i]['itemId'];
            $conceptoId = $results[$i]['conceptoId'];
            $categoria = $results[$i]['categoria'];
            $nivel = $results[$i]['nivel'];
            $concepto = $results[$i]['cuenta'].' - '.$results[$i]['nombre'];
            $moneda = $results[$i]['moneda'];
            $monto = $results[$i]['monto'];
            $rate = (isset($monedas[$moneda])) ? $monedas[$moneda] : 1;
            $total = $monto * $rate;

            $budget_total += $total;

            if($categoria==1) {
                $rows .= "\t".'<tr class="level_'.$nivel.'">';
                $rows .= '<td style="white-space:nowrap;">'.space_by_level($nivel).$concepto."</td>";
                $rows .= '<td style="border-left:none;">&nbsp;</td>';
                $rows .= '<td style="border-left:none;">&nbsp;</td>';
                $rows .= '<td style="border-left:none;">&nbsp;</td>';
                $rows .= '<td style="border-left:none;">&nbsp;</td>';
                $rows .= '</tr>'."\n";
            } else {
                $rows .= "\t".'<tr class="level_'.$nivel.'">';
                $rows .= '<td>'.space_by_level($nivel).$concepto.'</td>';
                $rows .= '<td><select id="moneda_'.$itemId.'" name="monedas['.$itemId.']" style="width:65px;" onchange="calc_totals('.$itemId.');">';
                foreach($monedas as $key => $tc) {
                    $rows .= ($key==$moneda) ? '<option value="'.$key.'" selected>'.$key.'</option>' : '<option value="'.$key.'">'.$key.'</option>';
                }
                $rows .= '</select></td>';
                $rows .= '<td><input type="text" id="monto_'.$itemId.'" name="montos['.$itemId.']" value="'.$monto.'" onchange="calc_totals('.$itemId.');"></td>';
                $rows .= '<td id="row_td_total_'.$itemId.'">'.number_currency($total).'</td>';
                $rows .= '<td><a href="mod/budgets.php?cmd=item_del&id='.$budgetId.'&iid='.$itemId.'" title="Eliminar" onclick="return confirm(\'Está seguro que desea eliminar este rubro?\');"><img src="images/silk/cross.png" /></a></td>';
                $rows .= '</tr>'."\n";
            }

        }

        # row total
        $rows .= "\t".'<tr class="level_total">';
        $rows .= '<td style="border-top: 2px solid #7c7c7c;">&nbsp;</td>';
        $rows .= '<td style="border-left:none;border-top: 2px solid #7c7c7c;">&nbsp;</td>';
        $rows .= '<td style="border-left:none;border-top: 2px solid #7c7c7c;">Total</td>';
        $rows .= '<td style="border-left:none;border-top: 2px solid #7c7c7c;" id="budget_total">'.number_currency($budget_total).'</td>';
        $rows .= '<td style="border-left:none;border-top: 2px solid #7c7c7c;">&nbsp;</td>';
        $rows .= '</tr>'."\n";

    }

    return $rows;

}

# get next conceptoId by budget
function get_next_conceptoId($budgetId) {
    return (int)query_select_single_value("(conceptoId+1) AS conceptoId", TABLE_PROJECTS_BUDGETS_ITEMS, "presupuestoId = $budgetId", "conceptoId DESC");
}

function get_budget_search_date_from($date) {
    if(strtotime($date)==false) {
        $date = date("Y-m-01", strtotime(date("Y-m-d")." -3 months"));
    }
    return $date;
}

function get_budget_search_date_to($date) {
    if(strtotime($date)==false) {
        $date = date("Y-m-t", strtotime(date("Y-m-d")." +3 months"));
    }
    return $date;
}

# insert spaces &nbsp; in str based on cat level 0-2
function space_by_level($level) {
    $level *= 3;
    $str = "";
    for ($i=0; $i<$level; $i++) { 
        $str .= "&nbsp;";
    }
    return $str;
}

# load master budget into project's budget
function load_master_budget($projectId, $budgetId) {
    sql_query("INSERT INTO ".TABLE_PROJECTS_BUDGETS_ITEMS." (proyectoId, presupuestoId, conceptoId, parentId, categoria, nivel, cuenta, nombre) 
                SELECT $projectId, $budgetId, conceptoId, parentId, categoria, nivel, cuenta, nombre FROM ".TABLE_MASTER);
}

# get master budget's cats and subcats only (select options)
function get_master_budget_select_options($parentId) {

    $options = "";
    $results = sql_select("SELECT conceptoId, categoria, nivel, CONCAT(cuenta, ' - ', nombre) AS concepto
                            FROM ".TABLE_MASTER." WHERE nivel < 2 
                            ORDER BY cuenta ASC, categoria DESC, nivel ASC");
    
    if($results) {
        for($i=0; $i<count($results); $i++) {
            if($results[$i]['conceptoId']==$parentId) {
                $options .= '<option value="'.$results[$i]['conceptoId'].'" selected>'.space_by_level($results[$i]['nivel']).$results[$i]['concepto']."</option>\n";
            } else {
                $options .= '<option value="'.$results[$i]['conceptoId'].'">'.space_by_level($results[$i]['nivel']).$results[$i]['concepto']."</option>\n";
            }
        }
    }

    return $options;

}

# get all master budget's cats, subcats & concepts (table rows)
function get_master_budget_table_rows() {

    # vars
    global $global_perms;
    $rows = "";
    $results = sql_select("SELECT conceptoId, categoria, nivel, CONCAT(cuenta, ' - ', nombre) AS concepto 
                            FROM ".TABLE_MASTER." ORDER BY cuenta ASC, categoria DESC, nivel ASC");
    
    if($results) {

        for($i=0; $i<count($results); $i++) {

            $conceptoId = $results[$i]['conceptoId'];
            $categoria = $results[$i]['categoria'];
            $nivel = $results[$i]['nivel'];
            $concepto = $results[$i]['concepto'];;

            if($categoria==1) {
                $rows .= "\t<tr class=\"level_$nivel\">";
                $rows .= '<td>'.space_by_level($nivel).$concepto.'</td>';
                $rows .= '<td>';
                $rows .= ($global_perms['EDIT']) ? '<a href="master.edit.php?id='.$conceptoId.'" title="Editar"><img src="images/silk/pencil.png" /></a>' : '';
                $rows .= '</td>';
                $rows .= "</tr>\n";
            } else {
                $rows .= "\t<tr>";
                $rows .= '<td>'.space_by_level($nivel).$concepto.'</td>';
                $rows .= '<td>';
                $rows .= ($global_perms['EDIT']) ? '<a href="master.edit.php?id='.$conceptoId.'" title="Editar"><img src="images/silk/pencil.png" /></a>&nbsp;' : '';
                $rows .= ($global_perms['DELETE']) ? '<a href="mod/master.php?cmd=del&id='.$conceptoId.'" title="Eliminar" onclick="return confirm(\'Está seguro que desea eliminar este registro?\');"><img src="images/silk/cross.png" /></a>' : '';
                $rows .= '</td>';
                $rows .= "</tr>\n";
            }

        }

    }

    return $rows;

}

function add_budget($projectId, $directorId, $days, $date) {

    $budget['proyectoId'] = (int)$projectId;
    $budget['numero'] = get_budget_next_num($projectId);
    $budget['directorId'] = (int)$directorId;
    $budget['diasFilmacion'] = (int)$days;
    $budget['fechaDeRodaje'] = $date;

    $budgetId = query_insert(TABLE_PROJECTS_BUDGETS, $budget);

    if($budgetId>0) {

        # load master budget
        load_master_budget($projectId, $budgetId);

    }

    return $budgetId;

}

function budget_create_cierre_file($budgetId) {

    $budget = get_budget($budgetId);
    $budget_items = get_budget_items($budgetId);
    $project = get_project($budget['proyectoId']);
    $director = get_director($budget['directorId']);
    $cuenta = get_customer($project['cuentaId']);

    # Create PHPExcel object
    $objPHPExcel = new PHPExcel();

    # vars
    $currentRow = 2;
    
    # Set document properties
    $objPHPExcel->getProperties()->setCreator(session_get_data("name"))
                                ->setLastModifiedBy(session_get_data("name"))
                                ->setTitle("Cierre de Presupuesto");

    # Title
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$currentRow, 'LISTA DE CHEQUES');
    $objPHPExcel->getActiveSheet()->getStyle('B'.$currentRow)->applyFromArray( get_style("cierreTitle") );
    $currentRow += 2;

    # Info
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$currentRow, 'CLAVE');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$currentRow, $project['clave']);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$currentRow.':C'.$currentRow)->applyFromArray( get_style("cierreSubtitle") );
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$currentRow, 'PRESUPUESTO');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$currentRow, $budgetId);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$currentRow.':G'.$currentRow)->applyFromArray( get_style("cierreSubtitle") );
    $currentRow++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$currentRow, 'PROYECTO');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$currentRow, $project['titulo']);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$currentRow.':C'.$currentRow)->applyFromArray( get_style("cierreSubtitle") );
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$currentRow, 'CUENTA');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$currentRow, $cuenta['razonSocial']);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$currentRow.':G'.$currentRow)->applyFromArray( get_style("cierreSubtitle") );
    $currentRow++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$currentRow, 'PRODUCTO');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$currentRow, $project['producto']);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$currentRow.':C'.$currentRow)->applyFromArray( get_style("cierreSubtitle") );
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$currentRow, 'CLIENTE');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$currentRow, $project['cliente']);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$currentRow.':G'.$currentRow)->applyFromArray( get_style("cierreSubtitle") );
    $currentRow++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$currentRow, 'FECHA FILMACIÓN');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$currentRow, $budget['fechaDeRodaje']);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$currentRow.':C'.$currentRow)->applyFromArray( get_style("cierreSubtitle") );
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$currentRow, 'DIRECTOR');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$currentRow, $director['directorNombre']);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$currentRow.':G'.$currentRow)->applyFromArray( get_style("cierreSubtitle") );

    $currentRow += 2;

    # Data header
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, 'RFC');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$currentRow, 'Razon Social');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$currentRow, 'Nombre y Puesto');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$currentRow, 'Concepto');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$currentRow, 'Mail');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$currentRow, 'Comprobante');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$currentRow, 'Costo Unitario');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$currentRow, 'Dias');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$currentRow, 'Horas Extra');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$currentRow, 'Monto');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$currentRow, 'IVA');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$currentRow, 'Ret IVA');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$currentRow, 'Ret ISR');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$currentRow, 'Total');

    $objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':n'.$currentRow)->applyFromArray( get_style("cierreHeader") );

    # Set columns widths
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
    
    $currentRow++;
    
    # Conceptos
    if($budget_items) {

        $dataStartRow = $currentRow;

        for($i=0; $i<count($budget_items); $i++, $currentRow++) {

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$currentRow, $budget_items[$i]['concepto']);

            # Set formulas
            $objPHPExcel->getActiveSheet()->setCellValue("J".$currentRow, "=(G".$currentRow."*H".$currentRow.")+I".$currentRow);
            $objPHPExcel->getActiveSheet()->setCellValue("K".$currentRow, "=J".$currentRow."*".TAX_IVA);
            $objPHPExcel->getActiveSheet()->setCellValue("L".$currentRow, "=IF(F".$currentRow."=\"R\",K".$currentRow."/3*2,0)");
            $objPHPExcel->getActiveSheet()->setCellValue("M".$currentRow, "=IF(F".$currentRow."=\"R\",J".$currentRow."*".TAX_ISR.",0)");
            $objPHPExcel->getActiveSheet()->setCellValue("N".$currentRow, "=J".$currentRow."+K".$currentRow."-L".$currentRow."-M".$currentRow."");
            
        }

        # Set formats
		$objPHPExcel->getActiveSheet()->getStyle("G".$dataStartRow.":N".$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');

    }

    #$currentRow = $dataStartRow;

    # Set headers to send file
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Cierre Presupuesto '.$project['clave'].'.xlsx"');
    
    # Save & Send Excel 2007
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
        
}


/** Projects functions **/

# get distinct years from projects
function get_years_projects($company_id) {
    return sql_select("SELECT DISTINCT ano FROM ".TABLE_PROJECTS." WHERE companyId = $company_id AND activo = 1 AND deleted = 0 ORDER BY ano DESC");
}

# get all visible projects
function get_projects_visible($company_id) {
    return sql_select("SELECT *, IF(ano = '', 'Sin año', ano) AS anio FROM ".TABLE_PROJECTS." WHERE companyId = $company_id AND activo = 1 AND deleted = 0 ORDER BY ano DESC, titulo ASC");
}

function get_projects_all($company_id) {
    return sql_select("SELECT * FROM ".TABLE_PROJECTS." WHERE companyId = $company_id AND deleted = 0 ORDER BY titulo ASC");
}

# get project
function get_project($projectId) {
    
    return sql_select_row(" SELECT *, 
                                    CONCAT('".PATH_PROJECTS."', uniqId, '/facturas/') AS pathFacturas, 
                                    CONCAT('".PATH_PROJECTS."', uniqId, '/transfers/') AS pathTransfers, 
                                    CONCAT('".PATH_PROJECTS."', uniqId, '/cierres/') AS pathCierres, 
                                    CONCAT('".PATH_PROJECTS."', uniqId, '/comprobantes/') AS pathComprobantes, 
                                    CONCAT('".PATH_PROJECTS."', uniqId, '/firmas/') AS pathFirmas, 
                                    CONCAT('".PATH_PROJECTS."', uniqId, '/contratos/') AS pathContratos, 
                                    CONCAT('".PATH_PROJECTS."', uniqId, '/crews/') AS pathCrews 
                                FROM ".TABLE_PROJECTS." 
                                WHERE proyectoId = $projectId
                            ");

}

# create folders
function project_create_paths($uniqId) {

    if( file_exists(PATH_PROJECTS) && is_dir(PATH_PROJECTS) ) {
        
        $project_path = PATH_PROJECTS.$uniqId."/";

        if( !file_exists($project_path) ) {
            mkdir($project_path);
            mkdir($project_path."facturas");
            mkdir($project_path."transfers");
            mkdir($project_path."cierres");
            mkdir($project_path."contratos");
            mkdir($project_path."crews");
        } else {
            if( !file_exists($project_path."facturas") ) {  mkdir($project_path."facturas"); }
            if( !file_exists($project_path."transfers") ) {  mkdir($project_path."transfers"); }
            if( !file_exists($project_path."cierres") ) {  mkdir($project_path."cierres"); }
            if( !file_exists($project_path."contratos") ) {  mkdir($project_path."contratos"); }
            if( !file_exists($project_path."crews") ) {  mkdir($project_path."crews"); }
        }

    } else {
        die("projects path missing...");
    }

}

# get concept info
function get_concept_info($itemId) {
    
    return sql_select_row("SELECT * FROM ".TABLE_PROJECTS_BUDGETS_ITEMS." WHERE itemId = $itemId");

}

# update concept with prepared statement (updating 500+ records)
function concept_update($itemId, $moneda, $monto, $total) {

    global $mysqli;

	// Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("UPDATE ".TABLE_PROJECTS_BUDGETS_ITEMS." SET moneda = ?, monto = ?, total = ? WHERE itemId = ?")) {

		$stmt->bind_param('sddi', $moneda, $monto, $total, $itemId);
        $stmt->execute();
        return @$mysqli->affected_rows;
	
	} else {

		// Prepared statement failed - Set error message
		set_alert("error", "Hubo un problema en la información, por favor intenta nuevamente.");
		return false;

	}
	
}

# get concept children
function get_concept_children($budgetId, $parentId) {
    
    return sql_select("SELECT * FROM ".TABLE_PROJECTS_BUDGETS_ITEMS." WHERE presupuestoId = $budgetId AND parentId = $parentId");

}

function set_concept_as_child($budgetId, $conceptId) {

    return query_update(TABLE_PROJECTS_BUDGETS_ITEMS, array("categoria" => 0), "presupuestoId = $budgetId AND conceptoId = $conceptId");

}

/** Customers functions **/

function get_customers() {
    return sql_select("SELECT * FROM ".TABLE_CUSTOMERS." WHERE deleted = 0");
}

function get_customer($customerId) {
    return sql_select_row("SELECT * FROM ".TABLE_CUSTOMERS." WHERE cuentaId = $customerId");
}


/** Directors functions */

function get_directors_visible() {
    return sql_select("SELECT * FROM ".TABLE_DIRECTORS." WHERE activo = 1 AND deleted = 0");
}

function get_directors_all() {
    return sql_select("SELECT * FROM ".TABLE_VENDORS." WHERE director = 1 AND deleted = 0 ORDER BY razonSocial ASC");
}

function get_director($directorId) {
    return sql_select_row("SELECT * FROM ".TABLE_DIRECTORS." WHERE directorId = $directorId");
}

function get_director_name($directorId) {
    return query_select_single_value("razonSocial", TABLE_VENDORS, "proveedorId = $directorId");
}


/** Vendor functions **/

function get_vendors() {
    return sql_select("SELECT * FROM ".TABLE_VENDORS." WHERE deleted = 0 ORDER BY razonSocial ASC");
}

function get_vendor($vendorId) {
    return sql_select_row(" SELECT  proveedorId, rfc, razonSocial, repseReq, repseNumero, repseAviso,  director, extranjero, agencia, email, 
                                    banco, cuenta, clabe, swift, aba, editar, tmp, 
                                IF(acta <> '', CONCAT('".PATH_VENDORS."', acta), FALSE) AS acta, 
                                IF(constancia <> '', CONCAT('".PATH_VENDORS."', constancia), FALSE) AS constancia, 
                                IF(opinionCumplimiento <> '', CONCAT('".PATH_VENDORS."', opinionCumplimiento), FALSE) AS opinionCumplimiento, 
                                IF(estadoDeCuenta <> '', CONCAT('".PATH_VENDORS."', estadoDeCuenta), FALSE) AS estadoDeCuenta, 
                                IF(identificacion <> '', CONCAT('".PATH_VENDORS."', identificacion), FALSE) AS identificacion,
                                IF(residencia <> '', CONCAT('".PATH_VENDORS."', residencia), FALSE) AS residencia, 
                                IF(repse <> '', CONCAT('".PATH_VENDORS."', repse), FALSE) AS repse, 
                                    constancia_fecha, opinionCumplimiento_fecha, residencia_fecha, repse_fecha 
                            FROM ".TABLE_VENDORS." 
                            WHERE proveedorId = $vendorId
                        ");
}

function get_vendor_type($rfc) {

    if(strlen($rfc)>3) {
        $char = substr($rfc, 3, 1);
    } else {
        return "PF";
    }

    if(is_numeric($char)) {
        return "PM";
    } else {
        return "PF";
    }

}

function get_vendor_id($rfc) {
    return (int)query_select_single_value("proveedorId", TABLE_VENDORS, "rfc = '$rfc' AND deleted = 0");
}

function add_get_vendor($rfc, $razon_social, $email, $contract_type="") {
    $vendor = sql_select_row("SELECT proveedorId, rfc, razonSocial, email, repseReq, extranjero FROM ".TABLE_VENDORS." WHERE rfc = '$rfc' AND deleted = 0");
    if($vendor===false) {
        $vendor['rfc'] = $rfc;
        $vendor['razonSocial'] = $razon_social;
        $vendor['email'] = $email;
        $vendor['repseReq'] = ($contract_type=="CON REPSE") ? 1 : -1;
        $vendor['extranjero'] = (var_is_valid_rfc($rfc)) ? 0 : 1;
        $id = query_insert(TABLE_VENDORS, $vendor);
        $vendor['proveedorId'] = $id;
        if($vendor['repseReq']==-1 && !vendor_has_carta_repse($id)) {
            vendor_add_carta_repse($vendor);
        }
    }
    return $vendor;
}

function get_vendor_po_info($poId) {

    return sql_select_row(" SELECT 	g.gastoId, g.concepto, IF(g.fechaDePago IS NULL, '-', g.fechaDePago) AS fechaDePago, 
                                    g.moneda, g.tipoDeCambio, g.monto, g.iva, g.retIVA, g.retISR, g.total, g.totalMXN, 
                                    g.pagoFormaId, g.pagoMetodoId, g.usoCfdiId, 
                                    p.proyectoId, CONCAT(p.clave, ' - ', p.titulo) AS proyecto, p.companyId, 
                                    g.facturaUuid, g.facturaInfo, g.referencia, g.notas, 
                                    c.razonSocial AS empresa, c.color, c.bgcolor, 
                                    CONCAT('".PATH_PROJECTS."', p.uniqId, '/facturas/', g.facturaNombre, '.pdf') AS facturaPDF, 
                                    CONCAT('".PATH_PROJECTS."', p.uniqId, '/facturas/', g.facturaNombre, '.xml') AS facturaXML, 
                                    CONCAT('".PATH_PROJECTS."', p.uniqId, '/transfers/', g.transfer) AS transfer, 
                                    CONCAT('".PATH_PROJECTS."', p.uniqId, '/comprobantes/', g.comprobante, '.pdf') AS comprobantePDF, 
                                    CONCAT('".PATH_PROJECTS."', p.uniqId, '/comprobantes/', g.comprobante, '.xml') AS comprobanteXML, 
                                    pf.claveFormaPago, CONCAT(pf.claveFormaPago, ' - ', pf.pagoForma) AS pagoForma, 
                                    pm.claveMetodoPago, CONCAT(pm.claveMetodoPago, ' - ', pm.pagoMetodo) AS pagoMetodo, 
                                    pu.claveUso, CONCAT(pu.claveUso, ' - ', pu.uso) AS usoCfdi, 
                                    ps.pagoStatusId, ps.pagoStatus, 
                                    v.proveedorId, v.rfc, v.razonSocial, v.extranjero, v.email, v.banco, v.cuenta, v.clabe, v.swift, v.aba 
                            FROM    ".TABLE_SAT_FORMA_PAGO." pf, ".TABLE_SAT_METODO_PAGO." pm, ".TABLE_SAT_USO_CFDI." pu, 
                                    ".TABLE_PAYMENTS_STATUS." ps, ".TABLE_VENDORS." v, ".TABLE_POS." g, ".TABLE_PROJECTS." p, ".TABLE_COMPANIES." c 
                            WHERE   g.proyectoId = p.proyectoId AND pf.pagoFormaId = g.pagoFormaId AND pm.pagoMetodoId = g.pagoMetodoId AND 
                                    pu.usoCfdiId = g.usoCfdiId AND ps.pagoStatusId = g.pagoStatusId AND 
                                    g.proveedorId = v.proveedorId AND p.companyId = c.companyId AND 
                                    g.proveedorId = ".session_get_data("userId")." AND g.gastoId = $poId 
                        ");

}

function vendor_document_upload($vendorId, $doc) {
    
    if($doc=="constancia") {
        $id = "C";
    } elseif($doc=="opinionCumplimiento") {
        $id = "O";
    } elseif($doc=="estadoDeCuenta") {
        $id = "E";
    } elseif($doc=="identificacion") {
        $id = "ID";
    } elseif($doc=="acta") {
        $id = "AC";
    } elseif($doc=="residencia") {
        $id = "RES";
    } elseif($doc=="repse") {
        $id = "REP";
    }

    $document = ( isset($_FILES[$doc]) && $_FILES[$doc]['size']>0 && $_FILES[$doc]['error']==0) ? $_FILES[$doc] : false;

    if($document!==false) {

        $new_file_name = file_filter_filename($vendorId."_".$id."_".$document['name']);

        if(file_upload($document['tmp_name'], PATH_VENDORS, $new_file_name)===true) {
            return $new_file_name;
        }

    }

    return false;

}

function vendor_valid_bank_info($vendor) {
    if($vendor['extranjero']==0) {
        if(var_is_empty($vendor['banco']) || var_is_empty($vendor['cuenta']) || var_is_empty($vendor['clabe']) ) {
            set_alert("warning", "La información de tus datos bancarios está incompleta, es necesario que la completes para poder recibir cualquier pago.");
            return false;
        }
    } else {
        if(var_is_empty($vendor['banco']) || var_is_empty($vendor['cuenta'])  || (var_is_empty($vendor['swift']) && var_is_empty($vendor['aba'])) ) {
            set_alert("warning", "La información de tus datos bancarios está incompleta, es necesario que la completes para poder recibir cualquier pago.");
            return false;
        }
    }
    return true;
}

function vendor_valid_acta($vendor) {
    if( (bool)VENDOR_REQ_ACTA==true && $vendor['extranjero']==0 && get_vendor_type($vendor['rfc'])=="PM") {
        if(!file_is_valid($vendor['acta'])) {
            set_alert("error", "Hace falta tu Acta Constitutiva, ésta es requisito para poder recibir cualquier pago.");
            return false;
        }
    }
    return true;
}

function vendor_valid_constancia($vendor) {
    if( (bool)VENDOR_REQ_CSF==true && $vendor['extranjero']==0 ) {
        if(file_is_valid($vendor['constancia'])) {
            if( !vendor_verify_doc_date($vendor['constancia_fecha']) ) {
                set_alert("error", "Es necesario que actualices tu Constancia de Situación Fiscal, ésta es requisito para poder recibir cualquier pago.");
                return false;
            }
        } else {
            set_alert("error", "Hace falta tu Constancia de Situación Fiscal, ésta es requisito para poder recibir cualquier pago.");
            return false;
        }
    }
    return true;
}

function vendor_valid_opinion_cumplimiento($vendor) {
    if( (bool)VENDOR_REQ_OC==true && $vendor['extranjero']==0 ) {
        if(file_is_valid($vendor['opinionCumplimiento'])) {
            if( !vendor_verify_doc_date($vendor['opinionCumplimiento_fecha']) ) {
                set_alert("error", "Es necesario que actualices tu Opinión del Cumplimiento (D32), éste es requisito para poder recibir cualquier pago.");
                return false;
            }
        } else {
            set_alert("error", "Hace falta tu Opinión del Cumplimiento (D32), éste es requisito para poder recibir cualquier pago.");
            return false;
        }
    }
    return true;
}

function vendor_valid_identificacion($vendor) {
    if((bool)VENDOR_REQ_ID==true && $vendor['extranjero']==0 && !file_is_valid($vendor['identificacion'])) {
        if(get_vendor_type($vendor['rfc'])=="PM") {
            set_alert("error", "Es necesaria la Identificación del representante legal, ésta es requisito para poder recibir cualquier pago.");
        } else {
            set_alert("error", "Es necesaria tu Identificación, ésta es requisito para poder recibir cualquier pago.");
        }
        return false;
    }
    return true;
}

function vendor_valid_repse($vendor) {
    if( (bool)VENDOR_REQ_REPSE==true && $vendor['extranjero']==0 ) {
        if( $vendor['repseReq']==0 || ( $vendor['repseReq']==1 && (trim($vendor['repseNumero'])=="" || trim($vendor['repseAviso'])=="") ) ) {
            set_alert("error", "Es necesario que actualices tu información sobre el REPSE, éste es requisito para poder recibir cualquier pago.");
            return false;
        }
    }
    return true;
}

function vendor_valid_comprobante_domicilio($vendor) {
    if( (bool)VENDOR_REQ_RESIDENCY==true ) {
        if(file_is_valid($vendor['residencia'])) {
            if( !vendor_verify_doc_date($vendor['residencia_fecha']) ) {
                set_alert("error", "Es necesario que actualices tu Comprobante de Domicilio, éste es requisito para poder recibir cualquier pago.");
                return false;
            }
        } else {
            set_alert("error", "Hace falta tu Comprobante de Domicilio, éste es requisito para poder recibir cualquier pago.");
            return false;
        }
    }
    return true;
}

function vendor_valid_estado_cuenta($vendor) {
    if((bool)VENDOR_REQ_EC==true && !file_is_valid($vendor['estadoDeCuenta'])) {
        set_alert("error", "Es necesario tu Estado de Cuenta, éste es requisito para poder recibir cualquier pago.");
        return false;
    }
    return true;
}

function vendor_all_contracts_signed($vendor) {
    if((bool)VENDOR_REQ_CONTRACT==true && vendor_has_contracts_pending($vendor['proveedorId'])) {
        set_alert("error", "Tienes contratos o NDAs pendientes por firmar, éstos son necesarios para poder recibir cualquier pago.");
        return false;
    }
    return true;
}

function vendor_all_comps_uploaded($vendor, $po) {
    if((bool)VENDOR_REQ_COMPLEMENTO==true && $po['pagoStatusId']==PAYMENT_STATUS_PENDING && vendor_has_complementos_pending($vendor['proveedorId'], $po['gastoId'])) {
        set_alert("error", "Tienes complementos pendientes por subir, es necesario subirlos para poder recibir cualquier pago.");
        return false;
    }
    return true;
}

function vendor_verify_repse_date($vendor) {
    if(is_array($vendor) && count($vendor)>0) {
        if($vendor['repseReq']==-1) {
            if(vendor_verify_doc_date($vendor['repse_fecha'], 1095)===true) {
                return true;
            }
            $repse = sql_select_row("SELECT * FROM ".TABLE_CONTRACTS_VENDORS." WHERE proyectoId = 0 AND proveedorId = ".$vendor['proveedorId']);
            if($repse) {
                return vendor_verify_doc_date($repse['firmaFecha'], 1095);
            }
        }
    }
    return false;
}
function vendor_verify_doc_date($doc_date, $days_limit=90) {

    $doc_date = (!is_null($doc_date)) ? $doc_date : date("Y-m-d", strtotime("-3 years"));
    $lastUpdated = date_create($doc_date);
    $interval = date_diff($lastUpdated, date_create());

    if($interval->days>=$days_limit) {
        return false;
    }

    return true;

}

function vendor_has_contracts_pending($vendorId) {
    $result = sql_select_row("  SELECT COUNT(*) AS total FROM ".TABLE_CONTRACTS_VENDORS." 
                                WHERE proveedorId = $vendorId AND (firmaStatusId = 1 OR firmaFecha IS NULL)");
    if((int)$result['total']>0) {
        return true;
    }
    return false;
}

function vendor_has_complementos_pending($vendorId, $poId) {
    if(!vendor_is_foreign($vendorId)) {
        return (int)query_select_single_value("COUNT(g.gastoId) AS total", TABLE_POS." g, ".TABLE_PROJECTS." p, ".TABLE_COMPANIES." c", 
                                                "g.proyectoId = p.proyectoId AND p.companyId = c.companyId AND proveedorId = $vendorId AND 
                                                gastoId <> $poId AND pagoMetodoId <> ".FACTURAS_TIPO_COMPROBACION." AND comprobante = '' AND 
                                                fechaDePago >= '2022-01-01' AND pagoStatusId = ".PAYMENT_STATUS_PAYED, "");
    } else {
        return 0;
    }
}

function vendor_verify_poll_submitted($vendorId, $projectId) {
    return sql_select_row("SELECT * FROM ".TABLE_POLLS_ANSWERS." WHERE proveedorId = $vendorId AND proyectoId = $projectId");
}

function vendor_allow_edit_info($vendorId) {

    $pos = sql_select("SELECT gastoId FROM ".TABLE_POS." WHERE proveedorId = $vendorId AND fechaDePago = '".date("Y-m-d")."'");

    $ven = query_select_single_value("editar", TABLE_VENDORS, "proveedorId = $vendorId");

    if($pos || (bool)$ven==false) {
        return false;
    }

    return true;

}

function get_vendors_exist() {

    $results = sql_select_row("SELECT COUNT(*) AS total FROM ".TABLE_VENDORS." WHERE deleted = 0");
    return ((int)$results['total']==0) ? false : true;

}

function get_vendor_po_allow_edit($pagoMetodoId, $facturaUuid, $pagoStatusId, $comprobanteXML) {

    if($facturaUuid=="") {
        return true;
    }

    if($pagoMetodoId!=FACTURAS_TIPO_COMPROBACION && $pagoStatusId==PAYMENT_STATUS_PAYED && !file_is_valid($comprobanteXML)) {
        return true;
    }

    return false;

}

function vendor_is_foreign($vendorId) {
    return (bool)query_select_single_value("extranjero", TABLE_VENDORS, "proveedorId = $vendorId");
}

function vendor_last_pos($vendorId, $projectId) {
    $query = sql_select_row("SELECT COUNT(gastoId) AS total FROM ".TABLE_POS." WHERE proveedorId = $vendorId AND proyectoId = $projectId");
    if($query && $query['total']<=1) {
        return true;
    }
    return false;
}

/** POs functions **/

function get_pos($projectId, $vendor, $statusId, $factura, $dateFrom, $dateTo) {

    $sql_project = "";
    if($projectId>0) {
        $sql_project = " AND g.proyectoId = $projectId";
    }

    $sql_vendor = "";
    if($vendor!="") {
        $sql_vendor = " AND (v.rfc LIKE '%$vendor%' OR v.razonSocial LIKE '%$vendor%' OR v.email LIKE '%$vendor%')";
    }

    $sql_status = "";
    if($statusId>0) {
        $sql_status = " AND g.pagoStatusId = $statusId";
    }

    $sql_factura = "";
    if($factura=="si") {
        $sql_factura = " AND g.facturaUuid <> ''";
    } elseif($factura=="no") {
        $sql_factura = " AND g.facturaUuid = ''";
    }

    $sql_date = "";
    if(strtotime($dateFrom)!==false && strtotime($dateTo)!==false) {
        $sql_date = " AND ( g.fechaDePago BETWEEN '$dateFrom' AND '$dateTo' OR g.fechaDePago IS NULL )";
    }

    return sql_select(" SELECT 	g.gastoId, g.fechaDePago, g.prontoPago, g.facturaUuid, g.comprobante, g.concepto, g.moneda, g.total, 
                                p.titulo, 
                                v.razonSocial, v.extranjero, v.banco, v.clabe, v.swift, v.aba, 
                                s.pagoStatusId, s.pagoStatus 
                        FROM 	".TABLE_POS." g, ".TABLE_PROJECTS." p, ".TABLE_VENDORS." v, ".TABLE_PAYMENTS_STATUS." s
                        WHERE 	g.proyectoId = p.proyectoId AND g.proveedorId = v.proveedorId AND 
                                p.companyId = ".session_get_data("companyId")." AND 
                                g.pagoStatusId = s.pagoStatusId AND p.activo = 1 
                                $sql_project $sql_vendor $sql_status $sql_factura $sql_date
                        ORDER BY g.gastoId DESC
                        LIMIT 0, 300
                    ");

}

function get_pos_search_date_from($date) {
    if(strtotime($date)==false) {
        $date = date("Y-m-01", strtotime(date("Y-m-d")." -2 month"));
    }
    return $date;
}

function get_pos_search_date_to($date) {
    if(strtotime($date)==false) {
        $date = date("Y-m-t", strtotime(date("Y-m-d")." +2 month"));
    }
    return $date;
}

function get_po_status($poId) {
    return (int)query_select_single_value("pagoStatusId", TABLE_POS, "gastoId = $poId");
}

function get_po_info($poId) {

    return sql_select_row("SELECT   g.gastoId, g.pagoDias, g.fechaDePago, g.prontoPago, g.concepto, g.moneda, g.tipoDeCambio, 
                                    g.monto, g.iva, g.retIVA, g.retISR, g.total, g.totalMXN, 
                                    g.pagoFormaId, g.pagoMetodoId, g.usoCfdiId, 
                                    g.facturaUuid, g.facturaInfo, g.referencia, g.notas, 
                                    p.proyectoId, p.companyId, p.uniqId, p.clave, p.titulo, 
                                    CONCAT('". PATH_PROJECTS . "', p.uniqId, '/facturas/') AS pathFacturas, 
                                    CONCAT('". PATH_PROJECTS . "', p.uniqId, '/transfers/') AS pathTransfers, 
                                    CONCAT('". PATH_PROJECTS . "', p.uniqId, '/comprobantes/') AS pathComprobantes, 
                                    CONCAT('". PATH_PROJECTS . "', p.uniqId, '/facturas/', g.facturaNombre, '.pdf') AS facturaPDF, 
                                    CONCAT('". PATH_PROJECTS . "', p.uniqId, '/facturas/', g.facturaNombre, '.xml') AS facturaXML, 
                                    CONCAT('". PATH_PROJECTS . "', p.uniqId, '/transfers/', g.transfer) AS transfer, 
                                    CONCAT('". PATH_PROJECTS . "', p.uniqId, '/transfers/', g.transfer2) AS transfer2, 
                                    CONCAT('". PATH_PROJECTS . "', p.uniqId, '/transfers/', g.transfer3) AS transfer3, 
                                    CONCAT('". PATH_PROJECTS . "', p.uniqId, '/comprobantes/', g.comprobante, '.pdf') AS comprobantePDF, 
                                    CONCAT('". PATH_PROJECTS . "', p.uniqId, '/comprobantes/', g.comprobante, '.xml') AS comprobanteXML, 
                                    v.*, 
                                    pf.claveFormaPago, CONCAT(pf.claveFormaPago, ' - ', pf.pagoForma) AS pagoForma, 
                                    pm.claveMetodoPago, CONCAT(pm.claveMetodoPago, ' - ', pm.pagoMetodo) AS pagoMetodo, 
                                    pu.claveUso, CONCAT(pu.claveUso, ' - ', pu.uso) AS usoCfdi, 
                                    ps.*, 
                                    co.rfc AS companyRFC
                            FROM    ".TABLE_POS." g, ".TABLE_PROJECTS." p, ".TABLE_VENDORS." v, ".TABLE_SAT_FORMA_PAGO." pf, 
                                    ".TABLE_SAT_METODO_PAGO." pm, ".TABLE_SAT_USO_CFDI." pu, ".TABLE_PAYMENTS_STATUS." ps, ".TABLE_COMPANIES." co
                            WHERE   g.proyectoId = p.proyectoId AND g.proveedorId = v.proveedorId AND g.pagoFormaId = pf.pagoFormaId AND 
                                    pm.pagoMetodoId = g.pagoMetodoId AND pu.usoCfdiId = g.usoCfdiId AND p.companyId = co.companyId AND 
                                    g.pagoStatusId = ps.pagoStatusId AND 
                                    g.gastoId = $poId");

}

function add_po_log($poId, $info, $debug=false) {
    $now = date("Y-m-d H:i:s");
    return query_insert(TABLE_POS_LOG, array("gastoId" => $poId, "fecha" => $now, "info" => $info), $debug);
}

function get_po_log($poId) {
    return sql_select("SELECT fecha, info FROM ".TABLE_POS_LOG." WHERE gastoId = $poId ORDER BY fecha ASC");
}

function get_payments_status() {
    return sql_select("SELECT * FROM ".TABLE_PAYMENTS_STATUS);
}

function get_payments_status_role($roleId, $pagoStatusId=0) {
    
    global $global_perms;
    
    $sql_status = "";
    if(!$global_perms['AUTHORIZE'] && $pagoStatusId!=PAYMENT_STATUS_AUTHORIZED) {
        $sql_status .= " AND pagoStatusId <> ".PAYMENT_STATUS_AUTHORIZED;
    }
    if(!$global_perms['PAY'] && $pagoStatusId!=PAYMENT_STATUS_PAYED) {
        $sql_status .= " AND pagoStatusId <> ".PAYMENT_STATUS_PAYED;
    }
    
    return sql_select("SELECT * FROM ".TABLE_PAYMENTS_STATUS." WHERE 1 $sql_status");
}

function pos_calc_payment_date($company_id, $days) {

    global $global_company;

    if($global_company===false) {
        $global_company = get_company_info($company_id);
    }

    $days = ((int)$days>0) ? $days : $global_company['pagoDias'];

    $now = date("Y-m-d");
    $weeks = strtotime("$now +$days days");
    $weekday = (int)date("N", $weeks);

    if((int)$weekday==(int)$global_company['pagoDiaDePago']) {
        $payment_date = date("Y-m-d", $weeks);
    } elseif($weekday<$global_company['pagoDiaDePago']) {
        $diff = (int)$global_company['pagoDiaDePago'] - (int)$weekday;
        $payment_date = date("Y-m-d", strtotime(date("Y-m-d", $weeks)." +$diff days"));
    } else {
        $weeks = strtotime(date("Y-m-d", $weeks)." +1 weeks");
        $diff = (int)$weekday - (int)$global_company['pagoDiaDePago'];
        $payment_date = date("Y-m-d", strtotime(date("Y-m-d", $weeks)." -$diff days"));
    }

    return $payment_date;

}


/** Invoices functions **/

function get_invoice_xml_info($filename) {

    # validate xml
    libxml_use_internal_errors(TRUE);
    $dom = new DOMDocument();
    $dom->load($filename);
    $errors = libxml_get_errors();

    if(empty($errors) || (is_array($errors) && count($errors)==1 && $errors[0]->code==99) ) {
        # read xml
        $cfdi = new CFDI($filename);
        $values = $cfdi->get_cfdi_info();
        return $values;
    }

    $new_file_name = file_filter_filename($new_file_name);

    return false;

}

function get_invoice_filename($invoice_info) {

    return $invoice_info['UUID'];

}

function get_comprobante_filename($filename) {

    return pathinfo($filename, PATHINFO_FILENAME);

}

function invoice_exists($uuid) {

    $invoice = sql_select_row("SELECT gastoId FROM ".TABLE_POS." WHERE facturaUuid = '$uuid'");
    if($invoice==false) {
        return false;
    }

    return true;

}

function complement_exists($uuid) {
    $result = sql_select_row("SELECT gastoId FROM ".TABLE_POS." WHERE complementoUuid = '$uuid'");
    if($result==false) {
        return false;
    }
    return true;
}

function document_upload($filename, $path, $new_file_name) {

    $upload = file_upload($filename, $path, $new_file_name);

    if($upload!==true) {
        set_alert("warning", $upload);
    }

    return $upload;

}

function cfdi_get_info($invoice_xml) {

    # vars
    $error = false;

    # validate xml
    libxml_use_internal_errors(TRUE);
    $dom = new DOMDocument();
    $dom->load($invoice_xml['tmp_name']);
    $errors = libxml_get_errors();

    if(empty($errors) || (is_array($errors) && count($errors)==1 && $errors[0]->code==99) ) {
        $cfdi = new CFDI($invoice_xml['tmp_name']);
        $invoice_info = $cfdi->get_cfdi_info();
    } else {
        $error = true;
        set_alert("error", "Hubo un error al procesar el CFDI (archivo xml) o el archivo es inválido.");
        if(session_get_data("userId")==1) {
            var_dump($errors);
            die();
        }
    }

    if($error==false && $invoice_info===false) {
        $error = true;
        set_alert("error", "Hubo un error al procesar el CFDI (archivo xml) o el archivo es inválido.");
    }

    if($error==false) {
        return $invoice_info;
    } else {
        return false;
    }

}

function cfdi_valida_full($posInfo, $invoice_info) {

    # vars
    $error = false;

    # invoice exists
    $invoice_exists = invoice_exists($invoice_info['UUID']);
    if($error==false && $invoice_exists==true) {
        $error = true;
        set_alert("error", "Esta factura ya fue subida anteriormente.");
    }

    # rfc receptor
    $invoice_rfc = trim(mb_strtoupper(str_replace(array(",", "."), "", $invoice_info['Receptor RFC'])));
    $pos_rfc = trim(mb_strtoupper(str_replace(array(",", "."), "", $posInfo['companyRFC'])));
    if($error==false && $invoice_rfc!=$pos_rfc) {
        $error = true;
        set_alert("error", "El RFC del receptor no coincide.");
    }

    # rfc emisor
    $invoice_rfc = trim(mb_strtoupper(str_replace(array(",", "."), "", $invoice_info['Emisor RFC'])));
    $pos_rfc = trim(mb_strtoupper(str_replace(array(",", "."), "", $posInfo['rfc'])));
    if($error==false && $invoice_rfc!=$pos_rfc) {
        $error = true;
        set_alert("error", "El RFC del emisor no coincide.");
    }

    # tipo cfdi
    if($error==false && $invoice_info['TipoDeComprobante']!="I") {
        $error = true;
        set_alert("error", "El tipo de comprobante debe ser I-Ingreso.");
    }

    # uso cfdi
    if($error==false && $invoice_info['UsoCFDI']!=$posInfo['claveUso']) {
        $error = true;
        set_alert("error", "El uso del CFDI debe ser ".$posInfo['claveUso'].".");
    }

    # forma de pago
    if($error==false && $invoice_info['FormaPago']!=$posInfo['claveFormaPago']) {
        $error = true;
        set_alert("error", "La forma de pago del CFDI debe ser ".$posInfo['claveFormaPago'].".");
    }

    # metodo de pago
    if($error==false && $invoice_info['MetodoPago']!=$posInfo['claveMetodoPago']) {
        $error = true;
        set_alert("error", "El método de pago del CFDI debe ser ".$posInfo['claveMetodoPago'].".");
    }

    # subtotal, taxes & total
    if($error==false) {
        $subtotal_min = ($posInfo['monto'] - (int)PAYMENT_AMOUNT_RANGE);
        $subtotal_max = ($posInfo['monto'] + (int)PAYMENT_AMOUNT_RANGE);
        $traslados_min = ($posInfo['iva'] - (int)PAYMENT_AMOUNT_RANGE);
        $traslados_max = ($posInfo['iva'] + (int)PAYMENT_AMOUNT_RANGE);
        $retenciones_min = (($posInfo['retIVA']+$posInfo['retISR']) - (int)PAYMENT_AMOUNT_RANGE);
        $retenciones_max = (($posInfo['retIVA']+$posInfo['retISR']) + (int)PAYMENT_AMOUNT_RANGE);
        $total_min = ($posInfo['total'] - (int)PAYMENT_AMOUNT_RANGE);
        $total_max = ($posInfo['total'] + (int)PAYMENT_AMOUNT_RANGE);
        if( $invoice_info['Subtotal']<$subtotal_min || $invoice_info['Subtotal']>$subtotal_max || 
            $invoice_info['Traslados']<$traslados_min || $invoice_info['Traslados']>$traslados_max || 
            $invoice_info['Retenciones']<$retenciones_min || $invoice_info['Retenciones']>$retenciones_max || 
            $invoice_info['Total']<$total_min || $invoice_info['Total']>$total_max 
        ) {
            $error = true;
            set_alert("error", "Los importes (Subtotal, Impuestos y/o Total) del CFDI no son correctos.");
        }
    }
    
    # validacion ante el sat
    if((bool)VALIDA_CFDI_SAT==true) {
        if($error==false && cfdi_valida_sat($invoice_info['UUID'], $invoice_info['Emisor RFC'], $invoice_info['Receptor RFC'], $invoice_info['Total'])==false) {
            $error = true;
            set_alert("error", "El CFDI no fue encontrado en el SAT o no se encuentra Vigente.");
        }
    }

    return $error;

}

function cfdi_valida_sat($uuid, $emisor, $receptor, $total) {

    # revisar si hay & en el rfc (pasar validación porque si no hay error)
    if(strpos($emisor, "&")!==false) {
        return true;
    }

    # formar cadena
    $soap = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/"><soapenv:Header/><soapenv:Body><tem:Consulta><tem:expresionImpresa>?re='.$emisor.'&amp;rr='.$receptor.'&amp;tt='.$total.'&amp;id='.$uuid.'</tem:expresionImpresa></tem:Consulta></soapenv:Body></soapenv:Envelope>';

    # encabezados
    $headers = array(
        'Content-Type: text/xml;charset=utf-8',
        'SOAPAction: http://tempuri.org/IConsultaCFDIService/Consulta',
        'Content-length: '.strlen($soap)
    );

    # consulta
    $url = 'https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $soap);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $res = curl_exec($ch);

    curl_close($ch);

    # parse result
    $xml = simplexml_load_string($res);
    $data = $xml->children('s', true)->children('', true)->children('', true);
    $data = json_encode($data->children('a', true), JSON_UNESCAPED_UNICODE);
    $data = json_decode($data, true);

    if(session_get_data("userId")==1) {
        if(!isset($data['CodigoEstatus']) || $data['CodigoEstatus']!="S - Comprobante obtenido satisfactoriamente." || !isset($data['Estado']) || $data['Estado']!="Vigente") {
            var_dump($data);
            die();
        }
    }

    # verify result
    if(isset($data['CodigoEstatus']) && $data['CodigoEstatus']=="S - Comprobante obtenido satisfactoriamente.") {
        if(isset($data['Estado']) && $data['Estado']=="Vigente") {
            return true;
        }
    }

    return false;

}


/** SAT tables functions **/

function get_sat_forma_pago() {
    return sql_select("SELECT *, CONCAT(claveFormaPago, ' - ', pagoForma) AS pagoFormaFull FROM ".TABLE_SAT_FORMA_PAGO);
}

function get_sat_metodo_pago() {
    return sql_select("SELECT *, CONCAT(claveMetodoPago, ' - ', pagoMetodo) AS pagoMetodoFull FROM ".TABLE_SAT_METODO_PAGO);
}

function get_sat_uso_cfdi() {
    return sql_select("SELECT *, CONCAT(claveUso, ' - ', uso) AS usoFull FROM ".TABLE_SAT_USO_CFDI);
}


/** Settings */

function load_settings() {
    $settings	= sql_select("SELECT configKey, configValue FROM ".TABLE_SETTINGS);
    if($settings) {
        foreach($settings as $config) {
            $key = $config['configKey'];
            define("$key", $config['configValue']);
        }
    }
}

function get_settings() {

    # vars
    global $global_perms;
    $settings = array();

    # query
    $sql_type = (!$global_perms['FULL']) ? 'WHERE configPublic = 1' : '';
    $results = sql_select("SELECT * FROM ".TABLE_SETTINGS." $sql_type ORDER BY configCat ASC, configName ASC");
    
    # process
    if($results) {
        foreach($results as $config) {
            if(class_exists($config['configType'])) {
                $options = ($config['configOptions']!="") ? json_decode($config['configOptions'], true) : '';
                $settings[] = new $config['configType']($config['configId'], $config['configCat'], $config['configKey'], $config['configName'], "span6 m-wrap", $config['configValue'], $options);
            }
        }
    }

    return $settings;

}

function get_settings_cats() {
    global $global_perms;
    $sql_type = (!$global_perms['FULL']) ? 'WHERE configPublic = 1' : '';
    return sql_select("SELECT DISTINCT configCat FROM ".TABLE_SETTINGS." $sql_type ORDER BY configCat ASC");
}

function get_setting($id) {
    return sql_select_row("SELECT * FROM ".TABLE_SETTINGS." WHERE configId = $id");
}


/** Nominas functions */

function get_nomina_info($nomina_id) {
    return sql_select_row("SELECT * FROM ".TABLE_WAGES." WHERE nominaId = $nomina_id");
}

function get_nominas($proyectoId) {

    $sql_proy = ((int)$proyectoId>0) ? " AND p.proyectoId = $proyectoId" : "";

    return sql_select(" SELECT n.*, p.titulo, CONCAT('".PATH_PROJECTS."', p.uniqId, '/cierres/') AS pathCierres
                        FROM ".TABLE_WAGES." n, ".TABLE_PROJECTS." p 
                        WHERE n.proyectoId = p.proyectoId AND p.companyId = ".session_get_data("companyId")." $sql_proy");

}

function wages_add($proyectoId, $archivo, $monto, $pos) {

    $values['proyectoId'] = $proyectoId;
    $values['fecha'] = date("Y-m-d");
    $values['archivo'] = $archivo;
    $values['monto'] = $monto;
    $values['pos'] = json_encode($pos);

    $id = query_insert(TABLE_WAGES, $values);

    system_log($id, TABLE_WAGES, "Add", json_encode($values));

    return $id;

}


/** Contracts functions */

function get_contracts() {
    return sql_select("SELECT * FROM ".TABLE_CONTRACTS." WHERE companyId = ".session_get_data("companyId")." ORDER BY sort ASC");
}

function get_contracts_vendors($projectId, $vendor, $statusId) {

    $sql_project = ($projectId>0) ? " AND p.proyectoId = $projectId" : "";

    $sql_vendor = ($vendor!="") ? " AND (v.rfc LIKE '%$vendor%' OR v.razonSocial LIKE '%$vendor%' OR v.email LIKE '%$vendor%')" : "";

    $sql_status = ($statusId>0) ? " AND cp.firmaStatusId = $statusId" : "";

    return sql_select(" SELECT p.proyectoId, IF(p.proyectoId = 0, 'Carta REPSE', p.titulo) AS titulo, v.razonSocial, 
                            CONCAT('".PATH_PROJECTS."', p.uniqId, '/contratos/', contrato) AS contrato, 
                            CONCAT('".PATH_PROJECTS."', p.uniqId, '/contratos/', anexo) AS anexo, 
                            CONCAT('".PATH_PROJECTS."', p.uniqId, '/contratos/', carta) AS carta, 
                            cp.id, cp.firmaStatusId, cp.firmaFecha, 
                            cs.contratoStatus
                        FROM ".TABLE_PROJECTS." p, ".TABLE_CONTRACTS_VENDORS." cp, ".TABLE_VENDORS." v, ".TABLE_CONTRACTS_STATUS." cs 
                        WHERE p.proyectoId = cp.proyectoId AND cp.proveedorId = v.proveedorId AND cp.firmaStatusId = cs.contratoStatusId AND 
                            p.companyId = ".session_get_data("companyId")." AND cp.parentId = 0 
                            $sql_project
                            $sql_vendor
                            $sql_status
                        ");

}

function get_contract($id) {
    return sql_select_row("SELECT * FROM ".TABLE_CONTRACTS." WHERE contratoId = $id");
}

function get_contract_vendor($id) {
    return sql_select_row("SELECT * FROM ".TABLE_CONTRACTS_VENDORS." WHERE id = $id");
}

function get_contract_type_for_vendor($rfc, $tipo="") {

    switch(trim($tipo)) {
        case 'persona fisica':
            $class_name = "VendorPF";
        break;
        case 'persona moral':
            $class_name = "VendorPM";
        break;
        case 'con repse':
            $class_name = "Repse".get_vendor_type($rfc);
        break;
        case 'talento':
            $class_name = "Talent";
        break;
        case 'post':
            $class_name = "ObraEncargo".get_vendor_type($rfc);
        break;
        default:
            $class_name = "Vendor".get_vendor_type($rfc);
        break;
    }
    
    return $class_name;

}

function vendor_has_carta_repse($vendorId) {
    return (bool)sql_select_row("SELECT cp.id FROM ".TABLE_CONTRACTS." c, ".TABLE_CONTRACTS_VENDORS." cp 
                                WHERE cp.contratoId = c.contratoId AND cp.proveedorId = $vendorId AND c.subtipo = 'CartaRepse'");
}

function vendor_add_carta_repse($vendorId, $fields_values="") {

    $values['proveedorId'] = $vendorId;
    $values['contratoId'] = (int)query_select_single_value("contratoId", TABLE_CONTRACTS, "subtipo = 'CartaRepse'");
    $values['fechaCreado'] = date("Y-m-d");
    $values['fieldsValues'] = $fields_values;
    $values['firma'] = "";
    $values['info'] = "";
    
    return query_insert(TABLE_CONTRACTS_VENDORS, $values);

}

function vendor_del_carta_repse($vendorId) {
    return sql_query("DELETE FROM ".TABLE_CONTRACTS_VENDORS." WHERE proveedorId = $vendorId AND 
                        contratoId = (SELECT contratoId FROM ".TABLE_CONTRACTS." WHERE subtipo = 'CartaRepse')");
}

function vendor_has_contract_for_project($vendorId, $proyectoId) {
    return sql_select_row("SELECT cv.id, cv.firmaStatusId, cv.fieldsValues, c.tipo, c.subtipo, c.nombre 
                            FROM ".TABLE_CONTRACTS." c, ".TABLE_CONTRACTS_VENDORS." cv 
                            WHERE c.contratoId = cv.contratoId AND cv.proveedorId = $vendorId AND cv.proyectoId = $proyectoId AND c.tipo = 'Contrato'");
}

function vendor_add_contract($vendor, $project, $tipo="", $fields_values="") {

    if(substr($project['titulo'], 0, 5)=="POST ") {
        $tipo = "post";
    }
    $subtype = "Contrato".get_contract_type_for_vendor($vendor['rfc'], $tipo);

    $values['proveedorId'] = $vendor['proveedorId'];
    $values['proyectoId'] = (int)$project['proyectoId'];
    $values['contratoId'] = (int)query_select_single_value("contratoId", TABLE_CONTRACTS, "subtipo = '$subtype'");
    $values['fechaCreado'] = date("Y-m-d");
    $values['fieldsValues'] = $fields_values;
    $values['firma'] = "";
    $values['info'] = "";
    
    return query_insert(TABLE_CONTRACTS_VENDORS, $values);

}

function vendor_add_adenda($vendor, $project_id, $parent_id, $subtype, $fields_values="") {

    if($subtype!="ContratoObraEncargoPM" && $subtype!="ContratoObraEncargoPF") {
        $values['parentId'] = $parent_id;
        $values['proveedorId'] = $vendor['proveedorId'];
        $values['proyectoId'] = $project_id;
        $values['contratoId'] = (int)query_select_single_value("contratoId", TABLE_CONTRACTS, "subtipo = '".str_replace("Contrato", "Adenda", $subtype)."'");
        $values['fechaCreado'] = date("Y-m-d");
        $values['fieldsValues'] = $fields_values;
        $values['firma'] = "";
        $values['info'] = "";
        return query_insert(TABLE_CONTRACTS_VENDORS, $values);
    }

    return false;

}

function vendor_remove_contract($vendorId, $proyectoId) {
    $record = sql_select_row("SELECT cv.id, cv.proveedorId, cv.proyectoId, 
                                CONCAT('".PATH_PROJECTS."', p.uniqId, '/contratos/', contrato) AS contrato, 
                                CONCAT('".PATH_PROJECTS."', p.uniqId, '/contratos/', anexo) AS anexo, 
                                CONCAT('".PATH_PROJECTS."', p.uniqId, '/contratos/', carta) AS carta 
                            FROM ".TABLE_CONTRACTS_VENDORS." cv, ".TABLE_PROJECTS." p 
                            WHERE cv.proyectoId = p.proyectoId AND cv.proveedorId = $vendorId AND cv.proyectoId = $proyectoId;");
    if($record) {
        if(file_is_valid($record['contrato'])) { file_delete($record['contrato']); }
        if(file_is_valid($record['anexo'])) { file_delete($record['anexo']); }
        if(file_is_valid($record['carta'])) { file_delete($record['carta']); }
        return query_delete(TABLE_CONTRACTS_VENDORS, "proveedorId = $vendorId AND proyectoId = $proyectoId");
    }
    return 0;
}

function get_contract_fields($contract) {

    preg_match_all('/\{(.*?)\}/s', $contract, $matches);
    
    if(is_array($matches) && isset($matches[0])) {

        $search = array_unique($matches[0]);
        $names = array_unique($matches[1]);

    }

    $fields = array();
    if(isset($names) && count($names)>0) {
        for($i=0; $i<count($names); $i++) {
            $req = (substr($names[$i], -1)=="*") ? true : false;
            $field = ($req) ? substr($names[$i], 0, strlen($names[$i])-1) : $names[$i];
            $fields[] = array("search" => $search[$i], "field" => $field, "text" => str_replace(array("_", "*"), " ", $names[$i]), "req" => $req, "res" => "");
            
        }
    }

    return $fields;

}

function get_contracts_status() {
    return sql_select("SELECT * FROM ".TABLE_CONTRACTS_STATUS);
}

function get_contract_attach_filename($rfc) {
    return $rfc."_".uniqid()."_anexo.pdf";
}

function save_contract_attach($document, $path, $filename) {

    if(!file_exists($path)) {
        mkdir($path);
    }

    return file_upload($document['tmp_name'], $path, $filename);

}

function get_contracts_vendor($vendorId) {

    return sql_select(" SELECT 	p.proyectoId, IF(p.titulo = '', 'Carta REPSE', p.titulo) AS titulo, cp.*, cs.contratoStatus, c.razonSocial, 
                                IF(con.tipo IS NULL, '-', con.tipo) AS tipo, 
                                CONCAT('".PATH_PROJECTS."', p.uniqId, '/contratos/', cp.contrato) AS contrato, 
                                CONCAT('".PATH_PROJECTS."', p.uniqId, '/contratos/', cp.anexo) AS anexo, 
                                CONCAT('".PATH_PROJECTS."', p.uniqId, '/contratos/', cp.carta) AS carta, 
                                CONCAT('".PATH_PROJECTS."', p.uniqId, '/firmas/') AS pathFirmas, 
                                CONCAT('".PATH_PROJECTS."', p.uniqId, '/contratos/') AS pathContratos
                        FROM ".TABLE_PROJECTS." p, ".TABLE_COMPANIES." c, ".TABLE_CONTRACTS_STATUS." cs, ".TABLE_CONTRACTS_VENDORS." cp LEFT JOIN
                            ".TABLE_CONTRACTS." con ON cp.contratoId = con.contratoId
                        WHERE p.companyId = c.companyId AND p.proyectoId = cp.proyectoId AND cp.firmaStatusId = cs.contratoStatusId AND 
                                cp.proveedorId = $vendorId");

}

function contract_upload_attachment($id, $vendor_rfc, $project_path) {

    $document = ( isset($_FILES["anexo"]) && $_FILES["anexo"]['size']>0 && $_FILES["anexo"]['error']==0) ? $_FILES["anexo"] : false;
    if($document!==false) {
        $attach_name = get_contract_attach_filename($vendor_rfc);
        $uploaded = save_contract_attach($document, $project_path, $attach_name);
        if($uploaded===true) {
            return query_update(TABLE_CONTRACTS_VENDORS, array("anexo" => $attach_name), "id = $id");
        } else {
            set_alert("warning", "Hubo un problema al subir el anexo - $uploaded");
        }
    }

    return false;

}


/** Banks functions */

function get_banks() {
    return sql_select("SELECT * FROM ".TABLE_BANKS." WHERE deleted = 0");
}

/** validations **/

function is_valid_clabe($string) {
    preg_match('/[0-9]{18}/i', $string, $match);
    if(var_is_valid_array($match)) {
        return true;
    }
    return false;
}

/*
Un código SWIFT/BIC ha de contener:
- un código de banco de 4 letras
- un código de país de 2 letras
- un código de lugar de 2 letras o 2 números
- un código opcional de sucursal de 3 letras o números (opcional)
*/
function is_valid_swift($string) {
    preg_match('/[a-z]{4}[a-z]{2}[a-z0-9]{2}[a-z0-9]{0,3}/i', $string, $match);
    if(var_is_valid_array($match)) {
        return true;
    }
    return false;
}


?>