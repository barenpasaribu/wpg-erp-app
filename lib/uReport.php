<?php



include_once 'lib/uElement.php';

class uReport
{
    public $_name;
    public $_page;
    private $_id;
    private $_primeEls;
    private $_advanceEls;

    public function uReport($cId, $cPage, $cName = null, $pEls = null, $aEls = null)
    {
        $this->_id = $cId;
        $this->_page = $cPage;
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
        $primeStr = '';
        $advanceStr = '';
        foreach ($this->_primeEls as $els) {
            $primeStr .= '##'.$els->_id;
        }
        foreach ($this->_advanceEls as $els) {
            $advanceStr .= '##'.$els->_id;
        }
        $fReport = "<div align='center'><h3>".$this->_name.'</h3></div>';
        $fReport .= '<fieldset><legend>Filter</legend>';
        $fReport .= "<div id='".$this->_id."'><table align='center'>";
        foreach ($this->_primeEls as $els) {
            $fReport .= '<tr><td>'.makeElement($els->_id.'_check', 'checkbox', 1, ['checked' => 'checked', 'onclick' => "toggleActive(this,'".$els->_id."')"]).'</td>';
            $fReport .= '<td>'.makeElement($els->_id, 'label', $els->_name).'</td>';
            $fReport .= '<td>:</td><td>'.$els->genEls().'</td></tr>';
        }
        $fReport .= "<tr><td colspan='4' align='center'>".makeElement('btnPreview', 'btn', 'Preview', ['onclick' => "print('preview','".$primeStr."','".$advanceStr."','".$this->_page."')"]).'</td></tr>';
        $fReport .= '</table></div></fieldset>';
        $fReport .= '<fieldset><legend>Print Format</legend>';
        $fReport .= "<div id='printFormat' align='center'>";
        $fReport .= "<img id='report_pdf' title='PDF' src='images/".$_SESSION['theme']."/pdf.jpg' ";
        $fReport .= "class='zImgPrint' onclick=\"print('pdf','".$primeStr."','".$advanceStr."','".$this->_page."')\"></img>&nbsp;";
        $fReport .= "<img id='report_xls' title='Excel' src='images/".$_SESSION['theme']."/excel.jpg' ";
        $fReport .= "class='zImgPrint' onclick=\"print('excel','".$primeStr."','".$advanceStr."','".$this->_page."')\"></img>";
        $fReport .= '</div></fieldset>';
        $fReport .= '<fieldset><legend>Preview</legend>';
        $fReport .= "<div id='workField'></div></fieldset>";

        return $fReport;
    }

    public function render()
    {
        echo $this->prep();
    }
}

?>