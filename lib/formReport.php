<?php



include_once 'lib/uElement.php';

class formReport
{
    public $_name;
    public $_primeEls;
    public $_advanceEls;
    public $_page;
    public $_workField;
    public $_detailHeight;
    private $_id;

    public function formReport($cId, $cPage, $cName = null, $pEls = null, $aEls = null)
    {
        $this->_id = $cId;
        $this->_page = $cPage;
        $this->_workField = 'workField';
        $this->_detailHeight = 70;
        null === $cName;
        (null === $cName ? ($this->_name = ucfirst($cId)) : ($this->_name = $cName));
        null === $pEls;
        (null === $pEls ? ($this->_primeEls = []) : ($this->_primeEls = $pEls));
        null === $aEls;
        (null === $aEls ? ($this->_advanceEls = []) : ($this->_advanceEls = $aEls));
    }

    public function addPrime($cId, $cName, $cCont = null, $cType = null, $cAlign = null, $cLength = null, $cRefer = null)
    {
        $this->_primeEls[] = new uElement($cId, $cName, $cCont, $cType, $cAlign, $cLength, $cRefer);
    }

    public function addAdvance($cId, $cName, $cCont = null, $cType = null, $cAlign = null, $cLength = null, $cRefer = null)
    {
        $this->_advanceEls[] = new uElement($cId, $cName, $cCont, $cType, $cAlign, $cLength, $cRefer);
    }

    public function prep()
    {
        global $dbname;
        $primeStr = '';
        $advanceStr = '';
        foreach ($this->_primeEls as $els) {
            switch ($els->_type) {
                case 'bulantahun':
                    $primeStr .= '##'.$els->_id.'_bulan##'.$els->_id.'_tahun';

                    break;
                default:
                    $primeStr .= '##'.$els->_id;
            }
        }
        foreach ($this->_advanceEls as $els) {
            switch ($els->_type) {
                case 'bulantahun':
                    $advanceStr .= '##'.$els->_id.'_bulan##'.$els->_id.'_tahun';

                    break;
                default:
                    $advanceStr .= '##'.$els->_id;
            }
        }
        $fReport = '';
        $fReport .= '<fieldset><legend><b>'.$this->_name.'</b></legend>';
        $fReport .= "<div id='".$this->_id."'><table align='left'>";
        foreach ($this->_primeEls as $els) {
            $fReport .= '<tr><td>'.makeElement($els->_id, 'label', $els->_name).'</td>';
            $fReport .= '<td>:</td><td>'.$els->genEls().'</td></tr>';
        }
        $fReport .= "<tr><td colspan='4' align='left'>".makeElement('btnPreview', 'btn', 'Preview', ['onclick' => "formPrint('preview',0,'".$primeStr."','".$advanceStr."','".$this->_page."',event)"]).makeElement('btnPDF', 'btn', 'PDF', ['onclick' => "formPrint('pdf',0,'".$primeStr."','".$advanceStr."','".$this->_page."',event)"]).makeElement('btnExcel', 'btn', 'Excel', ['onclick' => "formPrint('excel',0,'".$primeStr."','".$advanceStr."','".$this->_page."',event)"]).'</td></tr>';
        $fReport .= '</table></div></fieldset>';
        $fReport .= '<fieldset><legend><b>Preview</b></legend>';
        $fReport .= "<div id='".$this->_workField."' style='overflow:auto;height:".$this->_detailHeight."%'></div></fieldset>";

        return $fReport;
    }

    public function render()
    {
        echo $this->prep();
    }
}

?>